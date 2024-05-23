<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;

class PlanController extends Controller {

  public function index() {

    $yearly_plans = Plan::where("is_yearly", 1)->get();
    $monthly_plans = Plan::where("is_yearly", 0)->get();

    return view("pricing", compact("yearly_plans", "monthly_plans"));
  }

  public function show(Plan $plan, Request $request) {

    $intent = auth()->user()->createSetupIntent();
    // $intent = auth()->user()->pay(
    //   $plan->price
    // );
    return view("offer", compact("plan", "intent"));
  }


  // public function subscription(Request $request) {
  //   $plan = Plan::find($request->plan);

  //   $subscription = $request->user()->newSubscription($request->plan, $plan->stripe_plan)
  //       // ->withCoupon('code')
  //       ->create($request->token);

  //   return view("subscription_success");
  // }

}
