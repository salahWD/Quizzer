<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Quiz;
use App\Models\Answer;
use App\Models\Condition;
use App\Models\QuestionTranslation;
use App\Models\AnswerTranslation;
use App\Models\Entry;
use App\Models\Field;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Question extends Model implements TranslatableContract {
  use HasFactory;
  use Translatable;

  protected $fillable = [
    "quiz_id",
    "type",
    "order",
    "multi_select",
    "is_skippable",
    "show_policy",
    "image",
    "video",
  ];

  public $translatedAttributes = [
    "title",
    "description",
    "button_label",
  ];

  public $timestamps = false;

  public function quiz() {
    return $this->belongsTo(Quiz::class);
  }

  public function entries() {
    return $this->hasMany(Entry::class);
  }

  public function report_answers() {
    return $this->hasMany(Answer::class)
        ->select(["answers.id", "en.text AS en_text", "ar.text AS ar_text", DB::raw("COUNT(answer_entry.answer_id) AS selected_count")])
        ->join('answer_translations AS en', "en.answer_id", "=", DB::raw("answers.id AND en.locale = 'en'"))
        ->leftJoin('answer_translations AS ar', "ar.answer_id", "=", DB::raw("answers.id AND ar.locale = 'ar'"))
        ->leftJoin('answer_entry', "answer_entry.answer_id", "=", "answers.id")
        ->orderBy('answers.order')
        ->groupBy("answer_entry.answer_id");
  }

  public function answers() {
    return $this->hasMany(Answer::class)->orderBy('order');
  }

  public function translated_answers() {
    return $this->hasMany(Answer::class)->with("translations")->orderBy('order');
  }

  public function ar_answers() {
    return $this->hasMany(Answer::class)
        ->join('answer_translations', 'answer_translations.answer_id', '=', 'answers.id')
        ->select('answers.*', 'answer_translations.locale AS locale', 'answer_translations.text AS ar_text')
        ->where('locale', 'ar')
        ->orderBy('order')->get();
  }

  public function en_answers() {
    return $this->hasMany(Answer::class)
        ->join('answer_translations', 'answer_translations.answer_id', '=', 'answers.id')
        ->select('answers.*', 'answer_translations.locale AS locale', 'answer_translations.text AS en_text')
        ->where('locale', 'en')
        ->orderBy('order')->get();
  }

  public function langs_answers() {
    return $this->hasMany(Answer::class)
        ->join('answer_translations AS en', 'en.answer_id', '=', DB::Raw('answers.id AND en.locale = "en"'))
        ->leftJoin('answer_translations AS ar', 'ar.answer_id', '=', DB::Raw('answers.id AND ar.locale = "ar"'))
        ->select('answers.*', 'en.text AS en_text', 'ar.text AS ar_text')
        ->orderBy('order');
  }

  public function langs_translate() {
    $data = Question::join('question_translations AS en', 'en.question_id', '=', DB::Raw('questions.id AND en.locale = "en"'))
    ->leftJoin('question_translations AS ar', 'ar.question_id', '=', DB::Raw('questions.id AND ar.locale = "ar"'))
    ->select('en.title AS en_title', 'ar.title AS ar_title', 'en.description AS en_description', 'ar.description AS ar_description')
    ->where("questions.id", $this->id)
    ->get()->first();

    $this->en_title = $data->en_title;
    $this->en_description = $data->en_description;
    $this->ar_title = $data->ar_title;
    $this->ar_description = $data->ar_description;

    return true;

  }

  public function conditions() {
    return $this->hasMany(Condition::class);
  }

  public function integrations() {
    return $this->belongsToMany(Integration::class)->select("integrations.name", "integration_question.key", "integration_question.value");
  }

  public function fields() {
    return $this->hasMany(Field::class)->with("options")->orderBy("order");
  }

  public function has_integrations($search) {
    foreach ($this->integrations as $key => $value) {
      if ($value->name == $search) {
        return true;
      }
    }
    return false;
  }

  public function get_integration($name) {
    foreach ($this->integrations as $key => $value) {
      if ($value->name == $name) {
        return $this->integrations[$key];
      }
    }
    return false;
  }

  public function conditioned_answers() {
    return $this->hasOneThrough(Answer::class, Condition::class);
  }

  public function largest_order() {
    return Question::selectRaw('max(`order`) AS largest_order')->where('quiz_id', '=', $this->quiz_id)->get();
  }

  public function types() {
    return [
      1 => ["id" => 1, "icon" => "fa-text-width", "name" => "Text Question"],
      2 => ["id" => 2, "icon" => "fa-picture-o", "name" => "Image Question"],
      3 => ["id" => 3, "icon" => "fa-tasks", "name" => "Form Fields"],
      4 => ["id" => 4, "icon" => "fa-font", "name" => "Text"],
      5 => ["id" => 5, "icon" => "fa-file-video-o", "name" => "Image Or Video"],
    ];
  }

  public function add_view($question_id) {
    return Question::where("id", $question_id)->update(["views" => DB::raw("views + 1")]);
  }

}
