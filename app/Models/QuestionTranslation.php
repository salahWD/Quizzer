<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionTranslation extends Model {

  public $timestamps = false;

  public $fillable = [
    "title",
    "description",
    "button_label",
  ];

}
