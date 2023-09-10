<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Field;

class Option extends Model {
  use HasFactory;

  public $timestamps = false;

  protected $fillable = [
    "field_id",
    "value",
    "ar_value",
  ];

  function field() {
    return $this->belongsTo(Field::class);
  }

}
