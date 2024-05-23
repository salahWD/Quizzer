<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up() {
    Schema::create('granswers', function (Blueprint $table) {
      $table->id();
      $table->foreignId("answer_id")->nullable();
      $table->foreign('answer_id')->references('id')->on('answers')->onDelete('cascade');
      $table->string("tag", 7);
    });
  }

  public function down() {
    Schema::dropIfExists('granswers');
  }
};
