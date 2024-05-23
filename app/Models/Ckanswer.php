<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ckanswer extends Model {
  use HasFactory;

  protected $fillable = [
    "answer_id",
    "tag",
  ];

  public $table = "ckanswers";

  public $timestamps = false;

  public function answer() {
    return $this->belongsTo(Answer::class);
  }

}
