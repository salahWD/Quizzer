<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Auth\RegisterController;
use App\Models\User;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserController extends Controller {

  public function __construct() {
    $this->middleware('is_admin')->except("show", "update_profile", "subscription", "subscription_cancel");
  }

  public function index(Request $request) {
    $users = User::paginate(20);
    if ($users->currentPage() > $users->lastPage()) {
      return abort(404);
    }
    return view("users.manage", compact("users"));
  }

  public function show() {
    return view("users.profile", [
      'user' => auth()->user(),
    ]);
  }

  public function search(Request $request) {
    $request->validate([
      "email" => "required|string|max:255",
    ]);
    $res = User::where("email", 'like', "%" . $request->input("email") . "%")->paginate(20);

    foreach ($res as $item) {
      if ($item->subscription_end != null && $item->subscription_end > now()) {
        $item->sub_end = $item->subscription_end->diffForHumans();
      } else {
        $item->sub_end = "-";
      }
      $item->package_name = $item->get_package_name();
    }

    return $res;
  }

  public function user_info(Request $request, User $user) {

    $payment = $user->payments()->where("status", "completed")->orderBy("created_at", "DESC")->first();
    $payment_id = $payment != null && isset($payment->payment_id) ? $payment->payment_id : null;

    if (!$user->is_admin() && !empty($payment) && $payment->method != null) {
      if ($payment->method == "stripe") {

        $subscription = null;
        if ($payment_id != null) {

          \Stripe\Stripe::setApiKey(config("stripe.Secret_key"));
          $subscription = \Stripe\Subscription::retrieve($payment_id);
          $renew_date = Carbon::createFromTimeStamp($subscription->current_period_end)->format('d/m/Y');
        }

        $plan = $user->plan();

        return view('subscription_info', [
          "plan_name" => $plan->name . " " . ($plan->is_yearly == 1 ? "(" . __("Yearly") . ")" : "(" . __("Monthly") . ")") ?? null,
          "user" => $user,
          "renew_plan_date" => $user->subscription_end != null ? ($user->subscription_end->formatLocalized('%d/%m/%Y') ?? __("No Date")) : __("No Date"),
          "history_payment" => $user->payments_done,
          "quizzes" => $user->quizzes,
        ]);
      } elseif ($payment->method == "paypal") {

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $token = $provider->getAccessToken();
        $provider->setAccessToken($token);
        // $plan = $user->plan()->paypal_plan;// P-8H312152E29145839MT6COVA
        $subscription = null;
        if ($payment_id != null) {
          $subscription = $provider->showSubscriptionDetails($payment_id); // payment_id
        }

        $plan = $user->plan();

        return view('users.user-info', [
          "plan_name" => !empty($plan->name) ? ($plan->name . " " . ($plan->is_yearly == 1 ? "(" . __("Yearly") . ")" : "(" . __("Monthly") . ")")) : "--",
          "user" => $user,
          "renew_plan_date" => $user->subscription_end != null ? ($user->subscription_end->formatLocalized('%d/%m/%Y') ?? __("No Date")) : __("No Date"),
          "history_payment" => $user->payments_done,
          "quizzes" => $user->quizzes,
        ]);
      }
    } else if ($user->is_admin()) {

      $plan = $user->plan();
      return view('users.user-info', [
        "plan_name" => !empty($plan->name) ? ($plan->name . " " . ($plan->is_yearly == 1 ? "(" . __("Yearly") . ")" : "(" . __("Monthly") . ")")) : "--",
        "user" => $user,
        "renew_plan_date" => "∞",
        "history_payment" => $user->payments_done,
        "quizzes" => $user->quizzes,
      ]);
    } else {

      $plan = $user->plan();

      return view('users.user-info', [
        "plan_name" => !empty($plan->name) ? ($plan->name . " " . ($plan->is_yearly == 1 ? "(" . __("Yearly") . ")" : "(" . __("Monthly") . ")")) : "--",
        "user" => $user,
        "renew_plan_date" => $user->subscription_end != null ? ($user->subscription_end->formatLocalized('%d/%m/%Y') ?? __("No Date")) : __("No Date"),
        "history_payment" => $user->payments_done,
        "quizzes" => $user->quizzes,
      ]);
    }

    // return view("users.user-info", [
    //   'user' => $user,
    //   "plan_name" => $plan->name ?? null,
    // ]);
  }

  public function create(Request $request) {

    $packages = config("pricing.PRICING_PACKAGES");
    return view("users.create", compact("packages"));
  }

  public function edit(User $user, Request $request) {
    $packages = config("pricing.PRICING_PACKAGES");
    return view("users.create", compact("user", "packages")); // edit
  }

  public function store(Request $request) {

    if ($request->get("name") == null) {
      $request->request->add(['name' => 'new user']);
    }

    $request->validate([
      "name" => "required|string|max:255",
      "email" => "required|string|email|max:255|unique:users",
      "subscription_end" => "sometimes|nullable|date|after:yesterday",
      "status" => "required|numeric|min:0|max:7",
      "password" => "required|string|min:4",
    ]);

    User::create([
      'name' => request('name'),
      'email' => request('email'),
      'subscription_end' => request('subscription_end'),
      'status' => request('status'),
      'password' => Hash::make(request('password')),
    ]);

    return redirect()->route("show_users");
  }

  public function update(User $user, Request $request) {

    $request->validate([
      "name" => "required|string|max:255",
      "email" => "sometimes|string|email|max:255",
      "subscription_end" => "sometimes|nullable|date|after:yesterday",
      "status" => "required|numeric|min:0|max:7",
      "password" => "sometimes|nullable|string|min:4",
    ]);

    $user->name = request('name');
    if (request("email") != null) {
      $user->email = request('email');
    }
    $user->subscription_end = request('subscription_end');
    $user->status = User::Available_Levels[request('status')];
    if (request("password") != null) {
      $user->password = Hash::make(request('password'));
    }

    $res = $user->save();
    \Session::flash('edited', $res);
    return redirect()->route("show_users");
  }

  public function update_profile(Request $request) {

    $user = auth()->user();

    $request->validate([
      "first_name" => "string|max:255",
      "last_name" => "string|max:255",
      "email" => "required|email|max:255|unique:users,email," . $user->id . ",id",
      "password" => "sometimes|nullable|string|min:4",
    ]);

    $user->first_name = request('first_name');
    $user->last_name = request('last_name');
    if (request("email") != null) {
      $user->email = request('email');
    }
    if (request("password") != null) {
      $user->password = Hash::make(request('password'));
    }

    $user->save();
    \Session::flash('success', true);
    // $request->session()->put('success', true);
    return redirect()->route("profile");
  }

  public function subscription(Request $request) {

    $user = auth()->user();

    $payment = $user->payments()->where("status", "completed")->orderBy("created_at", "DESC")->first();
    $payment_id = $payment != null && isset($payment->payment_id) ? $payment->payment_id : null;

    if (!$user->is_admin() && !empty($payment) && $payment->method != null) {
      if ($payment->method == "stripe") {

        $subscription = null;
        if ($payment_id != null) {

          \Stripe\Stripe::setApiKey(config("stripe.Secret_key"));
          $subscription = \Stripe\Subscription::retrieve($payment_id);
          $renew_date = Carbon::createFromTimeStamp($subscription->current_period_end)->format('d/m/Y');
        }

        $plan = $user->plan();
        $package = $user->get_package();

        return view('subscription_info', [
          "plan_name" => $plan->name ?? null,
          "user" => $user,
          "package" => $package,
          "package_price" => $package["PRICE"] ?? "00",
          "renew_plan_features_date" => $user->subscription_end != null ? ($user->subscription_end->formatLocalized('%d/%m/%Y') ?? __("No Date")) : __("No Date"),
          "history_payment" => $user->payments_done,
          "next_payment" => isset($renew_date) ? $renew_date : null,
        ]);
      } elseif ($payment->method == "paypal") {

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $token = $provider->getAccessToken();
        $provider->setAccessToken($token);
        $subscription = null;
        if ($payment_id != null) {
          $subscription = $provider->showSubscriptionDetails($payment_id);
        }

        $plan = $user->plan();
        $package = $user->get_package();

        return view('subscription_info', [
          "plan_name" => $plan->name ?? null,
          "user" => $user,
          "package" => $package,
          "package_price" => $package["PRICE"] ?? "00",
          "renew_plan_features_date" => $user->subscription_end != null ? ($user->subscription_end->formatLocalized('%d/%m/%Y') ?? __("No Date")) : __("No Date"),
          "history_payment" => $user->payments_done,
          "next_payment" => isset($subscription["billing_info"]["next_billing_time"]) ? Carbon::parse($subscription["billing_info"]["next_billing_time"])->formatLocalized('%d/%m/%Y') : null,
        ]);
      }
    } else if ($user->is_admin()) {

      $plan = $user->plan();
      $package = $user->get_package();

      return view('subscription_info', [
        "plan_name" => $plan->name ?? null,
        "user" => $user,
        "package" => $package,
        "package_price" => "∞",
        "renew_plan_features_date" => "∞",
        "history_payment" => $user->payments_done,
        "next_payment" => "∞",
      ]);
    } else {

      $plan = $user->plan();
      $package = $user->get_package();

      return view('subscription_info', [
        "plan_name" => $plan->name ?? null,
        "user" => $user,
        "package" => $package,
        "package_price" => $package["PRICE"] ?? "00",
        "renew_plan_features_date" => $user->subscription_end != null ? ($user->subscription_end->formatLocalized('%d/%m/%Y') ?? __("No Date")) : __("No Date"),
        "history_payment" => $user->payments_done,
        "next_payment" => isset($subscription["billing_info"]["next_billing_time"]) ? Carbon::parse($subscription["billing_info"]["next_billing_time"])->formatLocalized('%d/%m/%Y') : null,
      ]);
    }
  }

  public function subscription_cancel(Request $request) {

    $user = auth()->user();

    $payment = $user->payments()->orderBy("created_at", "DESC")->first();
    $payment_id = $payment != null ? $payment->payment_id : null;

    if ($payment != null) {
      if ($payment->method == "stripe") {

        \Stripe\Stripe::setApiKey(config("stripe.Secret_key"));

        // $sub = \Stripe\Subscription::retrieve($payment_id);
        try {
          // $user->subscription()->cancel();// according to the docs
          if ($user->subscription($payment_id)->cancelNow()) {
            session()->flash('error', 'Unsubscribed package succesfully.');
          }
        } catch (Exception $ex) {
          session()->flash('error', 'Something went wrong.');
        }
        // $sub->cancel();

        DB::table("subscriptions")->where("stripe_id", $payment_id)->update([
          'trial_ends_at' => Carbon::now()->toDateTimeString(),
        ]);

        $user->subscription_end = now();
        $user->inactivate();
        $user->save();
      } elseif ($payment->method == "paypal") {

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $token = $provider->getAccessToken();
        $provider->setAccessToken($token);

        $response = $provider->cancelSubscription($payment_id, 'decided to end the service');

        if ($response == "") {
          $user->subscription_end = now();
          $user->inactivate();
          $user->save();
        }
      } else {
        $user->subscription_end = now();
        $user->inactivate();
        $user->save();
      }
    } else {
      $user->subscription_end = now();
      $user->inactivate();
      $user->save();
    }


    return redirect()->route('subscription_info');
  }

  public function destroy(User $user, Request $request) {
    $res = $user->delete();
    \Session::flash('is_deleted', $res);
    return redirect()->route("show_users");
  }
}
