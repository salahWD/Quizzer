<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Quiz;

class Quizgoogle extends Model {
  use HasFactory;

  protected $fillable = [
    "quiz_id",
    "start_marketing",
    "finish_marketing",
    "finish_conversion",
  ];

  public $timestamps = false;

  public function quiz() {
    return $this->belongsTo(Quiz::class);
  }

}
