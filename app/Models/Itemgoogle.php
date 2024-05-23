<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Quiz;

class Itemgoogle extends Model {
    use HasFactory;

    public $fillable = [
      'item_id',
      'item_type',
      'marketing_tag',
      'conversion_tag',
    ];

    public $timestamps = false;

    public function quiz() {
      return $this->belongsTo(Quiz::class);
    }

}
