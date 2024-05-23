<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Quiz;

class Gr extends Model {
  use HasFactory;

  protected $fillable = [
    "quiz_id",
    "type",
    "value_type",
    "value",
  ];

  public $table = "integrations_gr";

  public $timestamps = false;

  public function quiz() {
    return $this->belongsTo(Quiz::class);
  }

}
