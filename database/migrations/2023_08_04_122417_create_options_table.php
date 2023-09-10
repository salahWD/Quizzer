<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up() {
    Schema::create('options', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('field_id');
      $table->string("value");
      $table->string("ar_value")->nullable();
      $table->foreign('field_id')->references('id')->on('fields')->onDelete('cascade');
    });
  }

  public function down() {
    Schema::dropIfExists('options');
  }
};
