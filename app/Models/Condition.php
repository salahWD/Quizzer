<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;
use App\Models\Answer;

class Condition extends Model {
  use HasFactory;

  public $timestamps = false;

  protected $fillable = [
    "question_id",
    "target_type",
    "is_on",
    "any_or",
    "target_id",
  ];

  public function question() {
    return $this->belongsTo(Question::class);
  }

  public function answers() {
    return $this->belongsToMany(Answer::class);
  }

}
