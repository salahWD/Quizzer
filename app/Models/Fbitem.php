<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;
use App\Models\Result;

class Fbitem extends Model {
  use HasFactory;

  protected $fillable = [
    "item_id",
    "item_type",
    "event",
  ];

  public $timestamps = false;

  public function item() {
    if ($this->type == 1) {
      return $this->belongsTo(Question::class);
    }else {
      return $this->belongsTo(Result::class);
    }
  }

}
