<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;
use Carbon\Carbon;
use Laravel\Cashier\Cashier;
use App\Models\Plan;
use App\Models\Payment;
use App\Models\Paypal;

class User extends Authenticatable {
  use HasApiTokens, HasFactory, Notifiable, Billable;

  const Available_Levels = [
    0 => "inactive",
    1 => "level-1",
    2 => "level-2",
    3 => "level-3",
    4 => "level-1",
    5 => "level-2",
    6 => "level-3",
    7 => "admin",
  ];

  const Levels = [
    1 => "level-1",
    2 => "level-2",
    3 => "level-3",
    4 => "level-1",
    5 => "level-2",
    6 => "level-3",
  ];

  const Levels_Packages = [
    "level-1" => 1,
    "level-2" => 2,
    "level-3" => 3,
    "level-1" => 4,
    "level-2" => 5,
    "level-3" => 6,
  ];

  protected $dates = ['subscription_end']; // auto update probli

  protected $fillable = [
    'name',
    'email',
    'password',
    'status',
    'subscription_end',
  ];

  protected $hidden = [
    'password',
    'remember_token',
  ];

  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  public function websites() {
    return $this->hasMany(Website::class);
  }

  public function quizzes() {
    return $this->hasManyThrough(Quiz::class, Website::class);
  }

  public function allowed_websites() {
    if ($this->is_admin()) {
      return "∞";
    } elseif ($this->is_inactive()) {
      return 0;
    } else {
      return $this->get_package()["WEBSITES"];
    }
  }

  public function payments() {
    return $this->hasMany(Payment::class);
  }

  public function payments_done() {
    return $this->hasMany(Payment::class)->where("status", "completed");
  }

  public function total_responses_count() {
    return DB::select("SELECT COUNT(*) AS responses_count FROM (
      SELECT COUNT(submission_id) AS tot FROM `submissions` AS S
      inner join entries AS E ON E.submission_id = S.id
      inner join quizzes AS Q ON Q.id = S.quiz_id
      inner join websites AS W ON W.id = Q.website_id
      inner join users AS U ON U.id = W.user_id
      where U.id = " . $this->id . "
      GROUP BY S.id) src")[0]->responses_count;
  }

  public function allowed_responses() {
    if ($this->is_admin()) {
      return "∞";
    } elseif ($this->is_inactive()) {
      return 0;
    } else {
      return $this->get_package()["RESPONSES"];
    }
  }

  public function check_subsicription() {

    /* ======= VERSION 1.0 (was made for checkout not subscriptions) ======= */
    // if (!$this->is_admin() && $this->subscription_end != null && now() > $this->subscription_end) {// subscription is done
    //   if ($this->took_trial == 0) {
    //     $this->took_trial = 1;
    //   }
    //   $this->status = "inactive";
    //   $this->save();
    // }

    /* ======= VERSION 2.0 (with subscriptions) ======= */
    if (!$this->is_admin() && $this->subscription_end != null && now() > $this->subscription_end) { // subscription is done

      $payment = $this->payments()->orderBy("created_at", "DESC")->first(); // get last payment

      if ($payment != null) {
        if ($payment->method == "paypal") {

          $paypal = new Paypal([
            "token" => $payment->payment_id,
          ]);

          $details = $paypal->subscription_details();
          if (!$details["status"]) { // should be true
            $this->inactivate();
          } else { // active subscription
            $this->subscription_end = $details["next_billing_time"];
          }
        } elseif ($payment->method == "stripe") {

          $subscription = $this->subscribed($payment->package_id);
          $id = $this->payments()->orderBy("created_at", "DESC")->first()->payment_id;
          if ($subscription == true) { // active subscription
            $this->subscription_end = Carbon::createFromTimestamp($this->subscription($payment->package_id)->asStripeSubscription()->current_period_end)->toDateTimeString();
          } else {
            $this->inactivate();
          }
        }
        $this->save();
      } else {
        $this->took_trial = 1;
        $this->inactivate();
        $this->save();
      }
    }
  }

  public function is_admin() {
    return $this->status == "admin";
  }

  public function is_inactive() {
    return $this->status == "inactive";
  }

  public function is_sub() {
    if ($this->is_admin()) {
      return true;
    }
    if ($this->is_inactive()) {
      return false;
    }
    return in_array($this->status, Self::Levels) ?? false;
  }

  public function inactivate() {
    $this->took_trial = 1;
    return $this->status = "inactive";
  }

  public function can_open_quiz() {
    if ($this->is_admin()) {
      return true;
    }
    if ($this->is_inactive()) {
      return false;
    }
    $packages = config("pricing.PRICING_PACKAGES");
    return $this->total_responses_count() < $packages[Self::Levels_Packages[$this->status]]["RESPONSES"];
  }

  public function can_add_website() {
    if ($this->is_admin()) {
      return true;
    }
    if ($this->is_inactive()) {
      return false;
    }
    $packages = config("pricing.PRICING_PACKAGES");
    return $this->websites->count() < $packages[Self::Levels_Packages[$this->status]]["WEBSITES"];
  }

  public function can_custom_domain() {
    if ($this->is_admin()) {
      return true;
    }
    if ($this->is_inactive()) {
      return false;
    }
    $packages = config("pricing.PRICING_PACKAGES");
    return $packages[Self::Levels_Packages[$this->status]]["CUSTOM_DOMAIN"];
  }

  public function can_translate() {
    // if ($this->is_admin()) {return true;}
    if ($this->is_inactive()) {
      return false;
    } else {
      return true;
    }
    // $packages = config("pricing.PRICING_PACKAGES");
    // return $packages[Self::Levels_Packages[$this->status]]["TRANSLATABLE"];
  }

  public function can_open_quiz_err() {
    return
      '<div class="mt-4">
        <div class="alert alert-danger m-0" style="width: fit-content">
          ' . __('you have reached the limit of responses, please') . '<a href="' . route("pricing") . '">' . __('upgrade') . '</a>' . __('your plane') . '
        </div>
      </div>';
  }

  public function get_remaining_dayes() {

    if ($this->subscription_end != null && $this->subscription_end > now()) {
      return $this->subscription_end->diffInDays(now());
    }
    return 0;
  }

  public function get_remaining_paid_value() {

    if ($this->is_admin()) {
      return 9999;
    } elseif (!$this->is_inactive() && $this->subscription_end != null && $this->subscription_end > now()) {
      $package = config("pricing.PRICING_PACKAGES." . static::Levels_Packages[$this->status]);
      if ($package["PRICE"] > 0) {
        $remaining_dayes = $this->subscription_end->diffInDays(now());
        return round($package["PRICE"] / 30 * $remaining_dayes, 1);
      }
    }
    return 0;
  }

  public function upgrade_or_renew_package($package_id) {

    $package = config("pricing.PRICING_PACKAGES.$package_id");

    $this->status = Self::Levels[$package_id];
    $this->subscription_end = now()->addDay(30)->toDateTimeString(); // $package["dayes"];// may add this option to packages

    return $this->save();
  }

  public function open_free_trial($package_id) {

    if ($this->took_trial == 0 && $this->subscription_end == null) {

      $dayes = config("pricing.FREE_TRIAL_DAYES");

      $this->status = Self::Levels[$package_id];
      $this->subscription_end = now()->addDay($dayes)->toDateTimeString();

      return $this->save();
    } else {
      return false;
    }
  }

  public function change_trial_package($package_id) {

    if ($this->is_trailing()) {

      $this->status = Self::Levels[$package_id] ?? "";
      return $this->save();
    }
    return false;
  }

  public function is_trailing() {

    if (auth()->user()->is_admin()) {
      return false;
    }

    return $this->took_trial == 0 && $this->subscription_end != null && $this->subscription_end > now();
  }

  public function took_trail() {

    if (auth()->user()->is_admin()) {
      return false;
    }

    return $this->took_trial == 1 && $this->subscription_end != null;
  }

  public function get_package_name() {
    if ($this->is_admin()) {
      return "admin";
    }
    if ($this->is_inactive()) {
      return "inactive";
    }
    if ($this->status == null || $this->status == "") {
      return "inactive";
    }
    $package = config("pricing.PRICING_PACKAGES." . static::Levels_Packages[$this->status]);
    return $package["NAME"];
  }

  public function get_package_id() {
    if ($this->is_admin()) {
      return 5;
    }
    if ($this->is_inactive()) {
      return 0;
    }
    if ($this->status == null || $this->status == "") {
      return 0;
    }
    $package = config("pricing.PRICING_PACKAGES." . static::Levels_Packages[$this->status]);
    return $package["ID"];
  }

  public function get_package() {
    if ($this->is_admin()) {
      return false;
    }
    if ($this->is_inactive()) {
      return false;
    }
    if ($this->status == null || $this->status == "") {
      return false;
    }
    $package = config("pricing.PRICING_PACKAGES." . static::Levels_Packages[$this->status]);
    return $package;
  }

  public function plan() {
    $plan_id = $this->get_package_id();
    return Plan::where("id", $plan_id)->first() ?? new Plan();
  }
}
