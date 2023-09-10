<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Integration;
use App\Models\Quiz;

class Ac extends Model {
  use HasFactory;

  public $fillable = [
    'quiz_id',
    'type',
    'value_type',
    'value',
  ];

  public $timestamps = false;
  protected $table = 'integrations_ac';

  public function quiz() {
    return $this->belongsTo(Quiz::class);
  }

}
