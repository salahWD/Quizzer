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
    $provider->setAccessToken($token);

    // set:
    // -- product as new "@package product"
    // -- fee as the monthly price of package (for first month only)
    // -- plan as new "@package plan"
    // -- subscription starts after one month (which we took the price of as a fee)
    // "becuse of the plan can only start on a feeture date"

    // $time = time();
    // $startDate = date('Y-m-d\\TH:i:s\\Z', $time);

    $startDate = now()->addDays(30);

    $order = $provider->addProduct($this->package_name . ' Product', $this->package_name . ' Product', 'SERVICE', 'SOFTWARE')
    ->addSetupFee($this->price)
    ->addPlanTrialPricing('DAY', config("pricing.FREE_TRIAL_DAYES"))
    ->addMonthlyPlan($this->package_name . ' Plan', $this->package_name . ' Plan', config("pricing.PRICING_PACKAGES.".$this->package_id.".PRICE"))
    ->setReturnAndCancelUrl(route('success_payment'), route('cancel_payment'))
    ->setupSubscription(auth()->user()->name, auth()->user()->email, $startDate);

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

  public function subscription_details() {
    $provider = new PayPalClient;
    $provider->setApiCredentials(config('paypal'));
    $token = $provider->getAccessToken();
    $provider->setAccessToken($token);
    $subscription_details = $provider->showSubscriptionDetails($this->token);
    return
        isset($subscription_details["status"]) && !empty($subscription_details["status"]) ?
        [
          "status" => $subscription_details["status"] == "ACTIVE" ?? false,
          "next_billing_time" => $subscription_details["billing_info"]["next_billing_time"],
        ] :
        ["status" => false];
  }

}
