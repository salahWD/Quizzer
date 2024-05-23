<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponController extends Controller {

  public function __construct() {
    $this->middleware(['auth', "is_admin"])->except("check");
  }

  public function index() {

    if (auth()->user()->is_admin()) {
      return view("coupons.index")->with([
        "coupons" => Coupon::paginate(20),
      ]);
    } else {
      return abort(404);
    }
  }

  public function show(Coupon $coupon) {

    $data = Coupon::select(DB::raw("sum(coupons.usage) AS all_usage"), DB::raw("((" . $coupon->usage . ") / sum(coupons.usage)) * 100.0 AS usage_percent"))
      ->first();
    $usage_percent = round($data->usage_percent, 1);
    $all_usage = intval($data->all_usage) - $coupon->usage;
    return view("coupons.info", compact("coupon", "usage_percent", "all_usage"));
  }

  public function store(Request $request) {

    if (auth()->user()->is_admin()) {

      $request->validate([
        "code" => "required|string|min:4|max:14",
        "amount" => "required|numeric|min:0",
        "type" => "required|numeric|min:1|max:2",
        "package_id" => "nullable|sometimes|numeric",
        "expire_date" => "nullable|sometimes|date|after:yesterday",
      ]);

      $expire_date = new Carbon(request("expire_date"));

      Coupon::create([
        "code" => request("code"),
        "amount" => request("amount"),
        "type" => request("type") ?? 1,
        "package_id" => request("package_id") ?? null,
        "expire_date" => $expire_date ?? null,
      ]);

      return redirect()->route("show_coupons");
    } else {
      return abort(404);
    }
  }

  public function edit(Coupon $coupon) {

    if (auth()->user()->is_admin()) {

      return view("coupons.create")->with([
        "coupon" => $coupon,
        "packages" => config("pricing.PRICING_PACKAGES"),
      ]);
    } else {
      return abort(404);
    }
  }

  public function update(Request $request, Coupon $coupon) {

    if (auth()->user()->is_admin()) {
      $request->validate([
        "code" => "required|string|min:4|max:14",
        "amount" => "required|numeric|min:0",
        "type" => "nullable|sometimes|numeric|min:1|max:2",
        "package_id" => "nullable|sometimes|numeric",
        "expire_date" => "nullable|sometimes|date|after:yesterday",
      ]);

      $coupon->code   = request("code");
      $coupon->amount = request("amount");

      if (request()->has("package_id")) {
        $coupon->package_id = request("package_id");
      }

      if (request()->has("expire_date")) {
        $expire_date = new Carbon(request("expire_date"));
        $coupon->expire_date = $expire_date;
      }

      if (request()->has("type")) {
        $coupon->type = request("type");
      }

      $coupon->save();

      return redirect()->route("show_coupons");
    } else {
      return abort(404);
    }
  }

  public function check(Request $request) {

    $request->validate([
      "code" => "required|string|min:4|max:14",
      "package" => "required|integer|min:1|max:" . end(config("pricing.PRICING_PACKAGES"))["ID"],
    ]);

    $coupon = Coupon::where("code", request('code'))
      ->where("expire_date", ">", now())
      ->select("amount", "type", "package_id")
      ->first();

    // if ($coupon->type == 2) {// percent
    //   $coupon->amount = ($coupon->amount / 100) * $price;
    // }

    if ($coupon != null && (($coupon->package_id != null && request("package") != null && $coupon->package_id == request("package")) || $coupon->package_id == null)) {
      return $coupon;
    } else {
      return response('', 400);
    }
  }

  public function destroy(Coupon $coupon) {
    if (auth()->user()->is_admin()) {
      $coupon->delete();
      return redirect()->route("show_coupons");
    } else {
      return abort(404);
    }
  }
}
