<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Answer;
use App\Models\Itemgoogle;
use App\Models\Fbitem;
use App\Models\Tkitem;
use App\Models\Twitem;
use App\Models\Scitem;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Result extends Model implements TranslatableContract {
  use HasFactory;
  use Translatable;

  const item_type = 2;// to differ btwn Q&R when getting quiz items

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

  public function fb_event() {
    return $this->hasOne(Fbitem::class, "item_id");
  }

  public function google_tags() {
    return $this->hasOne(Itemgoogle::class);
  }

  public function answers_for_questoin($question_id) {
    return $this->belongsToMany(Answer::class)
        ->join("questions AS Q", "answers.question_id", "=", "Q.id")
        ->where("Q.id", $question_id);
  }

  public function types() {
    return [
      6 => ["id" => 6, "icon" => "fa-file-text-o", "name" => __("Results Builder")],
      7 => ["id" => 7, "icon" => "fa-desktop", "name" => __("URL Redirect")],
      8 => ["id" => 8, "icon" => "fa-calendar", "name" => __("Calendly")],
    ];
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

  public function fb_integrations() {
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

  public function add_view($result_id) {
    return Result::where("id", $result_id)->update(["views" => DB::raw("views + 1")]);
  }

}
