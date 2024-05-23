<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Quiz;

class Casestudy extends Model {
  use HasFactory;

  protected $fillable = [
    "name",
    "person",
    "job_title",
    "desc",
    "quiz_id",
    "is_best",
    "image",
    "video",
    "story_title",
    "story",
    "goal_title",
    "goal",
    "solution",
    "key_1_title",
    "key_1_desc",
    "key_1_img",
    "key_2_title",
    "key_2_desc",
    "key_2_img",
    "key_3_title",
    "key_3_img",
    "key_3_desc",
    "result_title",
    "result_desc",
  ];

  public function quiz() {
    return $this->belongsTo(Quiz::class);
  }

}
