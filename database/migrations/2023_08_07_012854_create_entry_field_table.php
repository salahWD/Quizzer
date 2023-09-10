<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up() {
    Schema::create('entry_field', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('field_id');
      $table->unsignedBigInteger('entry_id');

      $table->string("value", 1000)->nullable();

      $table->unique(['field_id', 'entry_id']);
      $table->foreign('field_id')->references('id')->on('fields')->onDelete('cascade');
      $table->foreign('entry_id')->references('id')->on('entries')->onDelete('cascade');
    });
  }

  public function down() {
    Schema::dropIfExists('entry_field');
  }

};
