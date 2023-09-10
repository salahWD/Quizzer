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
        Schema::create('answer_result', function (Blueprint $table) {
          $table->id();
          $table->foreignId("answer_id");
          $table->foreign('answer_id')->references('id')->on('answers')->onDelete('cascade');
          $table->foreignId("result_id");
          $table->foreign('result_id')->references('id')->on('results')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('answer_result');
    }
};
