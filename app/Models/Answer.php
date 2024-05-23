<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;
use App\Models\Result;
use App\Models\Condition;
use App\Models\Entry;
use App\Models\Acanswer;
use App\Models\Granswer;
use App\Models\Ckanswer;
use App\Models\Fbanswer;
use App\Models\Tkanswer;
use App\Models\Twanswer;
use App\Models\Scanswer;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Answer extends Model implements TranslatableContract {
  use HasFactory;
  use Translatable;

  protected $fillable = [
    "question_id",
    "score",
    "order",
    "image",
  ];

  public $translatedAttributes = [
    "text",
  ];

  public $timestamps = false;

  public function quesiton() {
    return $this->belongsTo(Question::class);
  }

  public function results() {
    return $this->belongsToMany(Result::class);
  }

  public function conditions() {
    return $this->belongsToMany(Condition::class);
  }

  public function entries() {
    return $this->belongsToMany(Entry::class);
  }

  public function acanswer() {
    return $this->hasOne(Acanswer::class);
  }

  public function granswer() {
    return $this->hasOne(Granswer::class);
  }

  public function ckanswer() {
    return $this->hasOne(Ckanswer::class);
  }

  public function fb_event() {
    return $this->hasOne(Fbanswer::class);
  }

  public function tk_event() {
    return $this->hasOne(Tkanswer::class);
  }

  public function tw_event() {
    return $this->hasOne(Twanswer::class);
  }

  public function sc_event() {
    return $this->hasOne(Scanswer::class);
  }

}
