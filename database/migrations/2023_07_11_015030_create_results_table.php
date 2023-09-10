<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId("quiz_id");
            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            $table->tinyinteger("type");
            $table->integer("min_score")->nullable();
            $table->integer("max_score")->nullable();
            $table->boolean("show_score")->nullable();
            $table->string("result_link")->nullable();
            $table->boolean("show_button")->nullable();
            $table->boolean("show_social")->nullable();
            $table->boolean("send_UTM")->nullable();
            $table->boolean("send_data")->nullable();
        });
    }

    public function down() {
        Schema::dropIfExists('results');
    }
};
