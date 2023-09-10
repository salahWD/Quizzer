<?php

namespace App\Models;

use App\Models\Website;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Integration extends Model {
  use HasFactory;

  public $timestamps = false;

  public $fillable = [
    "name",
    "email",
    "key",
    "url",
  ];

  public function website() {
    return $this->belongsTo(Website::class);
  }

  public function questions() {
    return $this->belongsToMany(Question::class);
  }

  public function quiz() {
    return $this->belongsToMany('integration_quiz');
  }

}
