<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up() {
      Schema::create('conditions', function (Blueprint $table) {
        $table->id();
        $table->foreignId("question_id");
        $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        $table->tinyInteger("target_type");// 1 => question || 2 => result
        $table->unsignedBigInteger("target_id");// target item id (question or result)
        $table->boolean("any_or")->default(0);// 0 => any || 1 => or
        $table->boolean("is_on")->default(1);
      });
    }

    public function down() {
      Schema::dropIfExists('conditions');
    }
};
