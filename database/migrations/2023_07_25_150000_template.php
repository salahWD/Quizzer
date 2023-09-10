<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {



  public function up() {

    Schema::create('templates', function (Blueprint $table) {
      $table->id();
      $table->foreignId("quiz_id")->nullable();
      $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
      $table->string("title");
      $table->string("thumbnail")->nullable();
      $table->string("description")->nullable();
      $table->timestamps();
    });
  }

  public function down() {
      Schema::dropIfExists('templates');
  }

};
