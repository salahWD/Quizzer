<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizTranslation extends Model {

  public $timestamps = false;

  public $fillable = [
    'name',
    'intro_btn',
    'intro_title',
    'policy_label',
    'template_desc',
    'intro_description',
  ];

}
