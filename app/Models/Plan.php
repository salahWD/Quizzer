<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model {

  use HasFactory;

  protected $fillable = [
    'name',
    'slug',
    'stripe_plan',
    'price',
    'description',
  ];

  public function package() {
    return config("pricing.PRICING_PACKAGES." . $this->id);
  }
}
