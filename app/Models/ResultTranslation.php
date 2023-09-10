<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultTranslation extends Model {

  public $timestamps = false;

  protected $fillable = [
    "title",
    "description",
    "score_message",
    "button_label",
  ];

}
