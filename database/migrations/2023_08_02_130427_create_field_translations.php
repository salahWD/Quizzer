<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up() {
      Schema::create('field_translations', function (Blueprint $table) {
        $table->id();
        $table->string('locale')->index();
        $table->unsignedBigInteger('field_id');

        $table->string("label");
        $table->string("placeholder")->nullable();

        $table->unique(['field_id', 'locale']);
        $table->foreign('field_id')->references('id')->on('fields')->onDelete('cascade');
      });
    }

    public function down() {
      Schema::dropIfExists('field_translations');
    }
};
