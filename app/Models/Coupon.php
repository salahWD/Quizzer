<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model {
  use HasFactory;

  public $fillable = [
    "package_id",
    "code",
    "amount",
    "type",
    "expire_date",
  ];

  public function package() {

    if ($this->has_package()) {
      $package = config("pricing.PRICING_PACKAGES")[$this->package_id];
      return $package;
    } else {
      return null;
    }
  }

  public function has_package() {
    return isset($this->package_id) && !empty($this->package_id);
  }

  public function is_expired() {
    return now() > $this->expire_date;
  }
}
