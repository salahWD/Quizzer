<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Payment extends Model {
  use HasFactory;

  protected $table = 'payments';

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

  protected $fillable = [
    "payment_id",
    "user_id",
    "package_id",
    "amount",
    "method",
    "bank_bill",
    "status",
  ];

  public function __construct($info = null) {
    if (is_array($info) && count($info) > 0) {
      foreach ($info as $key => $value) {
        $this->$key = $value;
      }
    }
  }

  public function package_name() {
    return Self::Available_Levels[$this->package_id];
  }

  public function is_approved() {
    return $this->status == "completed";
  }

  public function get_package_name() {
    $package = config("pricing.PRICING_PACKAGES." . $this->package_id);
    return $package["NAME"] ?? null;
  }

  public function user() {
    return $this->belongsTo(User::class);
  }

  public function get_currency() {
    return config("pricing.CURRENCY") ?? null;
  }
}
