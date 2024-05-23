<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up() {
      Schema::create('fbitems', function (Blueprint $table) {
          $table->id();
          $table->foreignId("item_id")->nullable();
          $table->tinyinteger("item_type")->nullable();/* 1 => question && 2 => quiz */
          $table->string("event")->nullable();
      });
    }

    public function down() {
      Schema::dropIfExists('fbitems');
    }

};
