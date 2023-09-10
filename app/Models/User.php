<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;

class User extends Authenticatable {
  use HasApiTokens, HasFactory, Notifiable, Billable;

  const Available_Packages = [
    "inactive" => 0,
    "level-1" => 1,
    "level-2" => 2,
    "level-3" => 3,
    "admin" => 4,
  ];

  const Available_Levels = [
    0 => "inactive",
    1 => "level-1",
    2 => "level-2",
    3 => "level-3",
    4 => "admin",
  ];

  const Levels = [
    1 => "level-1",
    2 => "level-2",
    3 => "level-3",
  ];

  const Levels_Packages = [
    "level-1" => 1,
    "level-2" => 2,
    "level-3" => 3,
  ];

  protected $dates = ['subscription_end'];// auto update probli

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

  public function allowed_websites() {
    if ($this->is_admin()) {
      return "∞";
    }elseif($this->is_inactive()) {
      return 0;
    }else {
      return $this->get_package()["WEBSITES"];
    }
  }

  public function payments() {
    return $this->hasMany(Payment::class);
  }

  public function total_responses_count() {
    return DB::table("submissions AS S")
        ->selectRaw("COUNT(S.id) AS responses_count")
        ->join("quizzes AS Q", "Q.id", "=", "S.quiz_id")
        ->join("websites AS W", "W.id", "=", "Q.website_id")
        ->join("users AS U", "U.id", "=", "W.user_id")
        ->where("U.id", $this->id)
        ->get()->first()->responses_count;
  }

  public function allowed_responses() {
    if ($this->is_admin()) {
      return "∞";
    }elseif($this->is_inactive()) {
      return 0;
    }else {
      return $this->get_package()["RESPONSES"];
    }
  }

  public function check_subsicription() {

    if (!$this->is_admin() && $this->subscription_end != null && now() > $this->subscription_end) {
      if ($this->took_trial == 0) {
        $this->took_trial = 1;
      }
      $this->status = "inactive";
      $this->save();
    }

  }

  public function is_admin() {
    return $this->status == "admin";
  }

  public function is_inactive() {
    return $this->status == "inactive";
  }

  public function is_sub() {
    return in_array($this->status, Self::Levels);
  }

  public function can_open_quiz() {
    if ($this->is_admin()) {return true;}
    if ($this->is_inactive()) {return false;}
    $packages = config("pricing.PRICING_PACKAGES");
    return $this->total_responses_count() < $packages[Self::Levels_Packages[$this->status]]["RESPONSES"];
  }

  public function can_add_website() {
    if ($this->is_admin()) {return true;}
    if ($this->is_inactive()) {return false;}
    $packages = config("pricing.PRICING_PACKAGES");
    return $this->websites->count() < $packages[Self::Levels_Packages[$this->status]]["WEBSITES"];
  }

  public function can_custom_domain() {
    if ($this->is_admin()) {return true;}
    if ($this->is_inactive()) {return false;}
    $packages = config("pricing.PRICING_PACKAGES");
    return $packages[Self::Levels_Packages[$this->status]]["CUSTOM_DOMAIN"];
  }

  public function can_translate() {
    if ($this->is_admin()) {return true;}
    if ($this->is_inactive()) {return false;}
    $packages = config("pricing.PRICING_PACKAGES");
    return $packages[Self::Levels_Packages[$this->status]]["TRANSLATABLE"];
  }

  public function can_open_quiz_err() {
    return
      '<div class="mt-4">
        <div class="alert alert-danger m-0" style="width: fit-content">
          you have reached the limit of responses, please <a href="' . route("pricing") . '">upgrade</a> your plane
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
    }elseif (!$this->is_inactive() && $this->subscription_end != null && $this->subscription_end > now()) {
      $package = config("pricing.PRICING_PACKAGES." . Static::Levels_Packages[$this->status]);
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
    $this->subscription_end = now()->addDay(30)->toDateTimeString();// $package["dayes"];// may add this option to packages

    return $this->save();

  }

  public function open_free_trial($package_id) {

    if ($this->took_trial == 0 && $this->subscription_end == null) {

      $dayes = config("pricing.FREE_TRIAL_DAYES");

      $this->status = Self::Levels[$package_id];
      $this->subscription_end = now()->addDay($dayes)->toDateTimeString();

      return $this->save();

    }else {
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
    if ($this->is_admin()) {return "admin";}
    if ($this->is_inactive()) {return "inactive";}
    $package = config("pricing.PRICING_PACKAGES." . Static::Levels_Packages[$this->status]);
    return $package["NAME"];
  }

  public function get_package_id() {
    if ($this->is_admin()) {return 5;}
    if ($this->is_inactive()) {return 0;}
    $package = config("pricing.PRICING_PACKAGES." . Static::Levels_Packages[$this->status]);
    return $package["ID"];
  }

  public function get_package() {
    if ($this->is_admin()) {return false;}
    if ($this->is_inactive()) {return false;}
    $package = config("pricing.PRICING_PACKAGES." . Static::Levels_Packages[$this->status]);
    return $package;
  }

}
