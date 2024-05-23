<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class Tkitem extends Model {
  use HasFactory;

  protected $fillable = [
    "item_id",
    "item_type",
    "event",
  ];

  public $timestamps = false;

  public $table = "tkitems";

  public function item() {
    return $this->morphTo();
  }

}
