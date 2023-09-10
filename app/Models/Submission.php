<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Entry;
use App\Models\Field;
use App\Models\Quiz;

class Submission extends Model {
  use HasFactory;

  protected $fillable = [
    "quiz_id",
    "is_done",
  ];

  public function quiz() {
    return $this->belongsTo(Quiz::class);
  }

  public function entries() {
    return $this->hasMany(Entry::class);
  }

  public function form_entries() {
    return $this->hasMany(Entry::class)->with("fields_value");
  }

  public function lead() {
    return $this->hasManyThrough(Field::class, Entry::class);
  }

  public function get_lead() {
    foreach($this->entries as $entry) {
      $fields = $entry->fields()->withPivot('value')->get();
      foreach($fields as $field) {
        if ($field->type == 3 && $field->is_lead_email == 1) {
          return $field->pivot->value;
        }
      }
    }
    return null;
  }

  public function entries_with_questions_answers() {
    return $this->hasMany(Entry::class)
        ->select([
            "entries.*",
            "en.title AS en_question_title",
            "ar.title AS ar_question_title",
        ])
        ->join("questions", "questions.id", "=", "entries.question_id")
        ->join("question_translations AS en", "en.question_id", "=", DB::raw("entries.question_id AND en.locale = 'en'"))
        ->leftJoin("question_translations AS ar", "ar.question_id", "=", DB::raw("entries.question_id AND ar.locale = 'ar'"))
        ->orderBy("questions.order");
  }

}
