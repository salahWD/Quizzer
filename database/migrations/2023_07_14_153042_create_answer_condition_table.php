<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up() {
        Schema::create('answer_condition', function (Blueprint $table) {
          $table->foreignId("condition_id");
          $table->foreign('condition_id')->references('id')->on('conditions')->onDelete('cascade');
          $table->foreignId("answer_id");
          $table->foreign('answer_id')->references('id')->on('answers')->onDelete('cascade');
        });
    }


    public function down() {
        Schema::dropIfExists('answer_condition');
    }
};
