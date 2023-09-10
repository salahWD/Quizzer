<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {


    public function up() {
      Schema::create('submissions', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('quiz_id');
        // $table->string('code');
        // $table->unique('code');
        $table->boolean('is_done')->default(0);
        $table->timestamps();
        $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
      });
    }

    public function down() {
      Schema::dropIfExists('submissions');
    }

};
