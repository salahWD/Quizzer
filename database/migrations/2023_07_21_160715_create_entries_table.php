<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up() {
    Schema::create('entries', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('submission_id');
      $table->unsignedBigInteger('question_id')->nullable();
      $table->string('utm_name')->nullable();
      $table->string('utm_value')->nullable();
      $table->timestamps();
      $table->unique(['submission_id', 'question_id']);
      $table->foreign('submission_id')->references('id')->on('submissions')->onDelete('cascade');
      $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
    });
  }


    public function down() {
        Schema::dropIfExists('entries');
    }
};
