<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("quiz_id");
            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            $table->tinyinteger("type");
            $table->boolean("multi_select")->default(0);
            $table->string("image")->nullable();
            $table->string("video")->nullable();
            $table->boolean("show_policy")->nullable();
            $table->boolean("is_skippable")->nullable();
            $table->integer("views")->default(0);
            $table->integer("order");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
};
