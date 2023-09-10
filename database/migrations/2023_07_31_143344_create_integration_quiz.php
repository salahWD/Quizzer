<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {


  public function up() {
    Schema::create('integration_quiz', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('integration_id');
      $table->unsignedBigInteger('quiz_id');
      $table->string("key")->nullable();
      $table->string("value")->nullable();
      $table->unique(['integration_id', 'quiz_id']);
      $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
      $table->foreign('integration_id')->references('id')->on('integrations')->onDelete('cascade');
    });
  }

  public function down(){
    Schema::dropIfExists('integration_quiz');
  }
};
