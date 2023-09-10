<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;
use App\Models\Result;
use App\Models\Condition;
use App\Models\Entry;

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

}
