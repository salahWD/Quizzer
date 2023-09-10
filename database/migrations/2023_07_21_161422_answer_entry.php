<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {


  public function up() {
    Schema::create('answer_entry', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('entry_id');
      $table->unsignedBigInteger('answer_id');
      $table->foreign('entry_id')->references('id')->on('entries')->onDelete('cascade');
      $table->foreign('answer_id')->references('id')->on('answers')->onDelete('cascade');
    });
  }


  public function down() {
    Schema::dropIfExists('answer_entry');
  }
};
