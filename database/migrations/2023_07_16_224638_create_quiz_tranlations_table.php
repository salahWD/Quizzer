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
      Schema::create('quiz_translations', function (Blueprint $table) {
        $table->id('id');
        $table->unsignedBigInteger('quiz_id');
        $table->string('locale')->index();
        $table->string("name");
        $table->string("policy_label")->nullable()->default(NULL);
        $table->string("intro_title")->nullable();
        $table->string("intro_description")->nullable()->default(NULL);
        $table->string("intro_btn")->nullable();
        $table->string("template_desc")->nullable()->default(NULL);

        $table->unique(['quiz_id','locale']);
        $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quiz_translations');
    }
};
