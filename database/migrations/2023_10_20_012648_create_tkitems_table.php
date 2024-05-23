<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up() {
    Schema::create('tkitems', function (Blueprint $table) {
      $table->id();
      $table->morphs('item');
      $table->string("event")->nullable();
    });
  }

  public function down() {
    Schema::dropIfExists('tkitems');
  }
};
