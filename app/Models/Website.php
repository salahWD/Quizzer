<?php

namespace App\Models;

use App\Models\Integration;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
  use HasFactory;

  protected $fillable = ["url", "company", "user_id"];

  public function user() {
    return $this->belongsTo(User::class);
  }

  public function quizzes() {
    return $this->hasMany(Quiz::class)->with("translations");
  }

  public function integrations() {
    return $this->hasMany(Integration::class);
  }

  public function has_integration($integration_name) {
    return $this->hasMany(Integration::class)->where("name", $integration_name)->get()->count() > 0;
  }

  public function get_integration($integration_name) {
    foreach($this->integrations as $key => $val) {
      if ($val->name == $integration_name) {
        return $this->integrations[$key];
      }
    }
    return false;
  }

  public static function websites_list($user_id) {

    return Website::where("user_id", "=", $user_id)->get();

  }

  public function get_quizzes() {
    return $this->quizzes;
  }

}
