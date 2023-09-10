<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Answer;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Result extends Model implements TranslatableContract {
  use HasFactory;
  use Translatable;

  protected $fillable = [
    "quiz_id",
    "type",
    "min_score",
    "max_score",
    "show_score",
    "result_link",
    "show_button",
    "show_social",
    "send_UTM",
    "send_data",
  ];

  protected $translatedAttributes = [
    "title",
    "description",
    "score_message",
    "button_label",
  ];

  public $timestamps = false;

  public function quiz() {
    return $this->belongsTo(Quiz::class);
  }

  public function answers() {
    return $this->belongsToMany(Answer::class);
  }

  public function answers_for_questoin($question_id) {
    return $this->belongsToMany(Answer::class)
        ->join("questions AS Q", "answers.question_id", "=", "Q.id")
        ->where("Q.id", $question_id);
  }

  public function types() {
    return [
      6 => ["id" => 6, "icon" => "fa-file-text-o", "name" => "Results Builder"],
      7 => ["id" => 7, "icon" => "fa-desktop", "name" => "URL Redirect"],
      8 => ["id" => 8, "icon" => "fa-calendar", "name" => "Calendly"],
    ];
  }

}
