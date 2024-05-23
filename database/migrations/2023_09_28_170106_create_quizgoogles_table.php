<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up() {
    Schema::create('quizgoogles', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('quiz_id');
      $table->text("start_marketing")->nullable();
      $table->text("finish_marketing")->nullable();
      $table->text("finish_conversion")->nullable();
      $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
    });
  }

  public function down() {
    Schema::dropIfExists('quizgoogles');
  }
};
