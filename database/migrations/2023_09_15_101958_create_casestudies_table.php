<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up() {
    Schema::create('casestudies', function (Blueprint $table) {
      $table->id();
      $table->string("name");
      $table->foreignId("quiz_id");
      $table->string("person");
      $table->boolean("is_best")->default(0);
      $table->string("job_title");
      $table->string("desc");
      $table->string("image");
      $table->string("video");
      $table->text("story");
      $table->string("story_title");
      $table->text("goal");
      $table->text("goal_title");
      $table->text("solution");
      $table->text("solution_img");
      $table->string("key_1_title");
      $table->text("key_1_desc");
      $table->string("key_1_img");
      $table->string("key_2_title")->nullable();
      $table->text("key_2_desc")->nullable();
      $table->string("key_2_img")->nullable();
      $table->string("key_3_title")->nullable();
      $table->string("key_3_img")->nullable();
      $table->text("key_3_desc")->nullable();
      $table->string("result_title");
      $table->text("result_desc");
      $table->timestamps();
      $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
    });
  }

  public function down() {
    Schema::dropIfExists('casestudies');
  }

};
