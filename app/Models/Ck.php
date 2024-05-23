<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ck extends Model {
  use HasFactory;

  public $fillable = [
    'quiz_id',
    'type',
    'value_type',
    'value',
  ];

  public $timestamps = false;
  protected $table = 'integrations_ck';

  public function quiz() {
    return $this->belongsTo(Quiz::class);
  }

}
