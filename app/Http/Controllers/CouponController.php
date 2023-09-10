<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponController extends Controller {


  public function __construct() {
    $this->middleware('auth');
  }

  public function index() {

    if (auth()->user()->is_admin()) {
      return view("coupons.index")->with([
        "coupons" => Coupon::paginate(20),
      ]);
    }else {
      return abort(404);
    }
  }

  public function store(Request $request) {

    if (auth()->user()->is_admin()) {

      $request->validate([
        "code" => "required|string|min:6|max:6",
        "amount" => "required|numeric|min:0",
        "package_id" => "nullable|sometimes|numeric",
        "expire_date" => "nullable|sometimes|date|after:yesterday",
      ]);

      $expire_date = new Carbon(request("expire_date"));

      Coupon::create([
        "code" => request("code"),
        "amount" => request("amount"),
        "package_id" => request("package_id") ?? null,
        "expire_date" => $expire_date ?? null,
      ]);

      return redirect()->route("show_coupons");

    }else {
      return abort(404);
    }
  }

  public function edit(Coupon $coupon) {

    if (auth()->user()->is_admin()) {

      return view("coupons.create")->with([
        "coupon" => $coupon,
        "packages" => config("pricing.PRICING_PACKAGES"),
      ]);
    }else {
      return abort(404);
    }
  }

  public function update(Request $request, Coupon $coupon) {

    if (auth()->user()->is_admin()) {
      $request->validate([
        "code" => "required|string|digits:6",
        "amount" => "required|numeric|min:0",
        "package_id" => "sometimes|nullable|numeric|min:0",
        "expire_date" => "sometimes|nullable|date|after:yesterday",
      ]);

      $coupon->code   = request("code");
      $coupon->amount = request("amount");

      if (request("package_id") !== null) {
        $coupon->package_id = request("package_id");
      }

      if (request("expire_date") !== null) {
        $coupon->expire_date = request("expire_date");
      }

      $coupon->save();

      return redirect()->route("show_coupons");
    }else {
      return abort(404);
    }
  }

  public function check(Request $request) {

    $request->validate([
      "code" => "required|string|min:6|max:6",
      "package" => "required|integer|min:1|max:3",
    ]);

    $coupon = Coupon::where("code", request('code'))
        ->where("expire_date", ">", now())
        ->select("amount", "package_id")
        ->first();

    if ($coupon != null && (($coupon->package_id != null && request("package") != null && $coupon->package_id == request("package")) || $coupon->package_id == null)) {
      return $coupon;
    }else {
      return response('', 400);
    }
  }

  public function destroy(Coupon $coupon) {
    if (auth()->user()->is_admin()) {
      $coupon->delete();
      return redirect()->route("show_coupons");
    }else {
      return abort(404);
    }
  }

}
