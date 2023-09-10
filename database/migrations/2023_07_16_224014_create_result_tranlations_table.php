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
    public function up() {
        Schema::create('result_translations', function (Blueprint $table) {
          $table->id();
          $table->string('locale')->index();
          $table->unsignedBigInteger('result_id');

          $table->string("title");
          $table->longText("description")->nullable();
          $table->string("score_message")->nullable();
          $table->string("button_label")->nullable();

          $table->unique(['result_id', 'locale']);
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
        Schema::dropIfExists('result_translations');
    }
};
