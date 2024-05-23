<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Answer;

class Granswer extends Model {
  use HasFactory;

  protected $fillable = [
    "answer_id",
    "tag",
  ];

  public $timestamps = false;

  public function answer() {
    return $this->belongsTo(Answer::class);
  }

}
