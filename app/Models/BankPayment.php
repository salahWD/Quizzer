<?php

namespace App\Models;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankPayment extends Payment {
  use HasFactory;

  public function pay() {

    $bank = BankPayment::create([
      "user_id" => auth()->id(),
      "amount" => $this->price,
      "package_id" => $this->package_id,
      "bank_bill" => $this->bank_bill,
      "status" => "pending",
      "method" => "bank",
    ]);

    return $bank != null ? true : false;

  }

}
