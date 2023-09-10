<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up() {
      Schema::create('integrations_ac', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('quiz_id');
        $table->string('type');
        $table->string('value_type');
        $table->string('value');
        $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
      });
    }

    public function down() {
      Schema::dropIfExists('integrations_ac');
    }
};
