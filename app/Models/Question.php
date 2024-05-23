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
use App\Models\Itemgoogle;
use App\Models\Fbitem;
use App\Models\Tkitem;
use App\Models\Twitem;


use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Question extends Model implements TranslatableContract {
  use HasFactory;
  use Translatable;

  const item_type = 1;// to differ btwn Q&R when getting quiz items

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

  public function fb_event() {
    return $this->hasOne(Fbitem::class, "item_id");
  }

  public function tk_event() {
    return $this->morphOne(Tkitem::class, 'item');
  }

  public function tw_event() {
    return $this->morphOne(Twitem::class, 'item');
  }

  public function sc_event() {
    return $this->morphOne(Scitem::class, 'item');
  }

  public function google_tags() {
    return $this->hasOne(Itemgoogle::class);
  }

  public function report_answers() {// translation issue
    return $this->answers()->with("translation")->withCount("entries");
  }

  public function answers() {
    return $this->hasMany(Answer::class)->orderBy('order');
  }

  public function is_mapped() {
    $res = $this->answers()->withCount("results")->get();
    if ($res->count() > 0) {
      foreach ($res as $ansr) {
        if ($ansr->results_count > 0) {
          return true;
        }
      }
    }
    return 0;
  }

  public function translated_answers() {
    return $this->hasMany(Answer::class)->with("translations")->orderBy('order');
  }

  public function ar_answers() {// translation issue
    return $this->hasMany(Answer::class)
        ->join('answer_translations', 'answer_translations.answer_id', '=', 'answers.id')
        ->leftJoin('fbanswers AS FBA', 'FBA.answer_id', '=', 'answers.id')
        ->select('answers.*', 'answer_translations.locale AS locale', 'answer_translations.text AS ar_text', 'FBA.event AS event')
        ->where('locale', 'ar')
        ->orderBy('order')->get();
  }

  public function en_answers() {// translation issue
    return $this->hasMany(Answer::class)
        ->join('answer_translations', 'answer_translations.answer_id', '=', 'answers.id')
        ->leftJoin('fbanswers AS FBA', 'FBA.answer_id', '=', 'answers.id')
        ->select('answers.*', 'answer_translations.locale AS locale', 'answer_translations.text AS en_text', 'FBA.event AS event')
        ->where('locale', 'en')
        ->orderBy('order')->get();
  }

  public function langs_answers() {
    return $this->hasMany(Answer::class)// translation issue
        ->join('answer_translations AS en', 'en.answer_id', '=', DB::Raw('answers.id AND en.locale = "en"'))
        ->leftJoin('answer_translations AS ar', 'ar.answer_id', '=', DB::Raw('answers.id AND ar.locale = "ar"'))
        ->leftJoin('fbanswers AS FBA', 'FBA.answer_id', '=', 'answers.id')
        ->select('answers.*', 'en.text AS en_text', 'ar.text AS ar_text', 'FBA.event AS event')
        ->orderBy('order');
  }

  public function langs_translate() {// translation issue
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

  public function active_conditions() {
    return $this->hasMany(Condition::class)->where("is_on", 1);
  }

  public function integrations() {
    return $this->belongsToMany(Integration::class)->select("integrations.name", "integration_question.key", "integration_question.value");
  }

  public function fb_integrations() {
    return $this->hasOne(Fbitem::class, "item_id");
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

  public function has_fb_integrations() {
    return $this->fb_integrations != null;
  }

  public function has_tk_integrations() {
    return $this->tk_event != null;
  }

  public function has_tw_integrations() {
    return $this->tw_event != null;
  }

  public function has_sc_integrations() {
    return $this->sc_event != null;
  }

  public function get_integration($name) {
    foreach ($this->integrations as $key => $value) {
      if ($value->name == $name) {
        return $this->integrations[$key];
      }
    }
    return false;
  }

  // public function conditioned_answers() {
  //   return $this->hasManyThrough(Condition::class, Answer::class);
  // }

  public function largest_order() {
    return Question::selectRaw('max(`order`) AS largest_order')->where('quiz_id', '=', $this->quiz_id)->get();
  }

  public function types() {
    return [
      1 => ["id" => 1, "icon" => "fa-text-width", "name" => __("Text Question")],
      2 => ["id" => 2, "icon" => "fa-picture-o", "name" => __("Image Question")],
      3 => ["id" => 3, "icon" => "fa-tasks", "name" => __("Form Fields")],
      4 => ["id" => 4, "icon" => "fa-font", "name" => __("Text")],
      5 => ["id" => 5, "icon" => "fa-file-video-o", "name" => __("Image Or Video")],
    ];
  }

  public function add_view($question_id) {
    return Question::where("id", $question_id)->update(["views" => DB::raw("views + 1")]);
  }

}
