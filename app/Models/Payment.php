<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model {
  use HasFactory;

  protected $table = 'payments';

  const Available_Levels = [
    0 => "inactive",
    1 => "level-1",
    2 => "level-2",
    3 => "level-3",
    4 => "admin",
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

}
