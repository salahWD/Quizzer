<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {


  public function up() {
    Schema::create('fields', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('question_id');
      $table->tinyInteger("type");
      $table->integer("order")->nullable();
      $table->boolean("is_required");
      $table->boolean("is_lead_email")->nullable();
      $table->boolean("is_multiple_chooseing")->nullable();
      $table->string("hidden_value")->nullable();
      $table->boolean("format")->nullable();
      $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
    });
  }


  public function down() {
    Schema::dropIfExists('fields');
  }

};
