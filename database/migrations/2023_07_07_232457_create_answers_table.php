<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up() {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId("question_id");
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->string('text');
            $table->integer('score')->nullable();
            $table->integer('order');
            $table->string('image')->nullable();
        });
    }

    public function down() {
        Schema::dropIfExists('answers');
    }

};
