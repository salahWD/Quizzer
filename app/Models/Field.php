<?php

namespace App\Models;

use App\Models\Question;
use App\Models\Option;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Field extends Model implements TranslatableContract {
  use HasFactory;
  use Translatable;

  public $timestamps = false;

  protected $fillable = [
    "question_id",
    "is_required",
    "is_lead_email",
    "is_multiple_chooseing",
    "hidden_value",
    "format",
    "type",
    "order",
  ];

  public $translatedAttributes = [
    "label",
    "placeholder",
  ];

  public function form() {
    return $this->belongsTo(Question::class);
  }

  public function options() {
    return $this->hasMany(Option::class);
  }

  public function type_name() {
    if ($this->type == 3) {
      return "email";
    }
    if (in_array($this->type, [4, 12])) {
      return "number";
    }
    if ($this->type == 5) {
      return "textarea";
    }
    if ($this->type == 7) {
      return "checkbox";
    }
    if ($this->type == 8) {
      return "select";
    }
    if ($this->type == 9) {
      return "time";
    }
    if ($this->type == 10) {
      return "date";
    }
    if ($this->type == 11) {
      return "url";
    }
    if ($this->type == 13) {
      return "hidden";
    }
    if ($this->type == 14) {
      return "utm_source";
    }
    if ($this->type == 15) {
      return "utm_medium";
    }
    if ($this->type == 16) {
      return "utm_campaign";
    }
    if ($this->type == 17) {
      return "utm_id";
    }
    if ($this->type == 18) {
      return "utm_term";
    }
    if ($this->type == 19) {
      return "utm_content";
    }
    return "text";
  }

}
