<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Submittion;
use App\Models\Answer;
use App\Models\Field;

class Entry extends Model {
  use HasFactory;

  protected $fillable = [
    "question_id",
    "submission_id",
  ];

  public function submittion() {
    return $this->belongsTo(Submittion::class);
  }

  public function answers() {
    return $this->belongsToMany(Answer::class);
  }

  public function fields() {
    return $this->belongsToMany(Field::class);
  }

  public function fields_value() {
    return $this->belongsToMany(Field::class)->select("entry_field.value");
  }

  public function fields_label_value($lang="en") {
    return $this->belongsToMany(Field::class)
        ->select("entry_field.value", "field_translations.label AS field_label")
        ->leftJoin("field_translations", "field_translations.field_id", "=", "entry_field.field_id")
        ->where("locale", $lang);
  }

  public function answers_report() {
    return $this->belongsToMany(Answer::class)
        ->select(["answers.id", "answers.image", "en.text AS en_text", "ar.text AS ar_text"])
        ->join("answer_translations AS en", "en.answer_id", "=", DB::raw("answers.id AND en.locale = 'en'"))
        ->leftJoin("answer_translations AS ar", "ar.answer_id", "=", DB::raw("answers.id AND ar.locale = 'ar'"));
  }

}
