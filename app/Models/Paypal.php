<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Session;
use App\Models\User;
use App\Models\Payment;

class Paypal extends Payment {
  use HasFactory;

  public function user() {
    return $this->belongsTo(User::class);
  }

  public function pay() {

    $provider = new PayPalClient;
    $provider->setApiCredentials(config('paypal'));
    $token = $provider->getAccessToken();

    $date = now()->addDays(1);

    $order = $provider->addProduct($this->package_name . ' Product', $this->package_name . ' Product', 'SERVICE', 'SOFTWARE')
    ->addMonthlyPlan($this->package_name . ' Plan', $this->package_name . ' Plan', $this->price)
    ->setReturnAndCancelUrl(route('success_payment'), route('cancel_payment'))
    ->setupSubscription(auth()->user()->name, auth()->user()->email, $date);

    if (isset($order["id"]) && $order["id"] != null) {
      foreach ($order["links"] as $link) {
        if ($link["rel"] == "approve") {

          $payment = Paypal::create([
            "user_id" => auth()->id(),
            "amount" => $this->price,
            "payment_id" => $order["id"],
            "package_id" => $this->package_id,
            "status" => "pending",
            "method" => "paypal",
          ]);

          header("Location: " . $link["href"]);
          exit();
        }
      }
    }

    header("Location: " . route("cancel_payment"));
    exit();

  }

  public function get_session() {

    $provider = new PayPalClient;
    $provider->setApiCredentials(config('paypal'));
    $token = $provider->getAccessToken();
    $response = $provider->capturePaymentOrder($this->token);

    return $response;

  }

}
