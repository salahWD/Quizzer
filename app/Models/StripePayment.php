<?php

namespace App\Models;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StripePayment extends Payment {
  use HasFactory;

  // public function pay() {
  //   // Set your secret key. Remember to switch to your live secret key in production.
  //   // See your keys here: https://dashboard.stripe.com/apikeys
  //   \Stripe\Stripe::setApiKey(config("stripe.Secret_key"));

  //   $order = \Stripe\Checkout\Session::create([
  //     'line_items' => [
  //       [
  //         'price_data' => [
  //           'currency' => config("pricing.CURRENCY_NAME"),
  //           'product_data' => [
  //             "name" => $this->package_name,
  //           ],
  //           'unit_amount' => $this->price * 100,
  //         ],
  //         'quantity' => 1,
  //       ],
  //     ],
  //     'mode' => 'payment',
  //     'success_url' => route("success_payment") . "?token={CHECKOUT_SESSION_ID}",
  //     'cancel_url' => route("cancel_payment"),
  //   ]);

  //   if (isset($order["id"]) && $order["id"] != null) {

  //     $payment = StripePayment::create([
  //       "user_id" => auth()->id(),
  //       "amount" => $this->price,
  //       "payment_id" => $order["id"],
  //       "package_id" => $this->package_id,
  //       "status" => "pending",
  //       "method" => "stripe",
  //     ]);

  //     header("Location: " . $order->url);
  //     exit();

  //   }else {
  //     return redirect()->route("cancel_payment");
  //   }

  // }


  public function pay() {

    $subscription = auth()->user()->newSubscription($this->package_id, $this->stripe_plan)
        ->create($this->token);

    if ($subscription != null && $subscription->id != null) {

      $payment = StripePayment::create([
        "user_id" => auth()->id(),
        "amount" => $this->price,
        "payment_id" => $subscription->stripe_id,
        "package_id" => $this->package_id,
        "status" => "pending",
        "method" => "stripe",
      ]);

      header("Location: " . route("success_payment", ["token" => $subscription->stripe_id]));
      exit();
    }else {
      return redirect()->route("cancel_payment");
    }


  }

  public function get_session() {

    \Stripe\Stripe::setApiKey(config("stripe.Secret_key"));

    $session = \Stripe\Checkout\Session::retrieve($this->token);
    if (!$session) {
      return null;
    }

    return $session;

  }

  public function webhook() {


    $stripe = new \Stripe\StripeClient(config("stripe.Secret_key"));

    $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
    $event = null;

    try {
      $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
      );
    } catch(\UnexpectedValueException $e) {
      // Invalid payload
      return response('', 400);
      // http_response_code(300);
      exit();
    } catch(\Stripe\Exception\SignatureVerificationException $e) {
      // Invalid signature
      // http_response_code(300);
      return response('', 400);
      exit();
    }


    // Handle the event
    switch ($event->type) {
      case 'checkout.session.completed':
        $session = $event->data->object;

        $payment = StripePayment::where("payment_id", $session->id)->first();

        if ($payment && $payment->status != "completed") {
          StripePayment::where("payment_id", $session->id)
              ->update(["status" => "completed"]);
        }

        default:
        echo 'Received unknown event type ' . $event->type;
      }

      return response('', 200);
      // http_response_code(200);
      exit();

  }

  }
