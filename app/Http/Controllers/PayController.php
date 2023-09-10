<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Paypal;
use App\Models\Payment;
use App\Models\StripePayment;
use App\Models\BankPayment;
use App\Models\Coupon;
use App\Models\Plan;

class PayController extends Controller {

  public function __construct() {
    $this->middleware('auth');
  }

  public function success(Request $request) {

    dd($request->all());

    $request->validate([
      "token" => "required|string|exists:payments,payment_id",
    ]);

    $payment = DB::table("payments")
        ->where("payment_id", request("token"))
        ->first();

    dd("success", $payment);

    if (!$payment) {
      return abort(404);
    }else {

      if ($payment->method == "paypal") {

        DB::table("payments")->where("payment_id", request("token"))->where("status", "pending")->update(["status" => "completed"]);

        auth()->user()->upgrade_or_renew_package($payment->package_id);// upgrade package

        return view("payment.success");

      }else if ($payment->method == "stripe") {

        StripePayment::where("payment_id", request("token"))->where("status", "pending")->update(["status" => "completed"]);

        auth()->user()->upgrade_or_renew_package($payment->package_id);// upgrade package

        return view("payment.success");

      }else if ($payment->method == "bank") {
        return redirect()->route("pending_payment");
      }else {
        redirect()->route("cancel_payment");
      }
    }

  }

  public function process(Plan $plan, Request $request) {

    $request->validate(
      [
        "payment_method" => "required|string|in:paypal,stripe,bank",
        "bank_bill" => "required_if:payment_method,bank|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte
        "coupon" => "sometimes|nullable|string|min:6|max:6",
      ],
      [
        "payment_method" => "No payment method has been selected",
        "bank_bill" => "please upload the (Bank Transfer)",
      ],
    );

    $package = $plan->package();
    $price = $package["PRICE"];
    $package_id = $package["ID"];

    if (request('coupon') != null && !empty(request('coupon'))) {

      $coupon = Coupon::where("code", request('coupon'))
          ->where("expire_date", ">", now())
          ->first();

      if ($coupon != null && (($coupon->package_id != null && $coupon->package_id == $package_id) || $coupon->package_id == null)) {
        $price = $price - $coupon->amount > 0 ? $price - $coupon->amount : 0;
        $coupon->usage = $coupon->usage + 1;
        $coupon->save();
      }

    }

    // if (auth()->user()->get_remaining_paid_value() > 0) {
    //   $paid_value = auth()->user()->get_remaining_paid_value();
    //   $price = $price - $paid_value > 0 ? $price - $paid_value : 0;
    // }

    if ($price <= 0) {
      auth()->user()->upgrade_or_renew_package($package_id);
      return redirect()->route("success_payment");
    }

    if (request("payment_method") == "stripe") {

      $request->validate([
        "token" => "required|string",
      ]);

      $stripe = new StripePayment([
        "token" => request('token'),
        "package_id" => $package_id,
        "stripe_plan" => $plan->stripe_plan,
        "price" => $plan->price,
      ]);

      // if ($price > 0 && $this->took_trial == 1) {
      $stripe->pay();
      // }
    }else if (request("payment_method") == "paypal") {

      $paypal = new Paypal([
        "price" => $price,
        "package_id" => $package_id,
        "paypal_plan" => $plan->paypal_plan,
        "package_name" => $plan->name,
      ]);

      $paypal->pay();

    }else if (request("payment_method") == "bank") {

      $image_name = date('mdYHis') . uniqid() . $request->file('bank_bill')->getClientOriginalName();
      $request->bank_bill->move(public_path('images/bills'), $image_name);

      $bank = new BankPayment([
        "price" => $price,
        "package_id" => $package_id,
        "bank_bill" => $image_name,
      ]);

      if ($bank->pay()) {
        return redirect()->route("pending_payment");
      }

      return redirect()->route("cancel_payment");

    }else {
      return abort(404);
    }

  }

  public function upgrade($package_id) {

    if (auth()->user()->change_trial_package($package_id)) {
      return redirect()->route("show_websites");
    }elseif (!auth()->user()->took_trail()) {
      auth()->user()->open_free_trial($package_id);
      return redirect()->route("show_websites");
    }
    return abort(404);

  }

  public function manage() {

    $payments = Payment::where("method", "bank")->orderBy("status")->paginate(20);
    return view("payment.approve", compact("payments"));
  }

  public function approve($payment_id) {

    $payment = Payment::where("id", $payment_id)->first();
    if (auth()->user()->is_admin()) {
      $payment->status = 'completed';
      $payment->save();
    }
    return redirect()->route('manage_payments');
  }

  public function destroy($payment_id) {

    if (auth()->user()->is_admin()) {
      Payment::where('id', $payment_id)->delete();
    }
    return redirect()->route('manage_payments');
  }

}
