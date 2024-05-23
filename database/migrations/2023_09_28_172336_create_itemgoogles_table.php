<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up() {
        Schema::create('itemgoogles', function (Blueprint $table) {
            $table->id();
            $table->foreignId("item_id")->nullable();
            $table->tinyinteger("item_type")->nullable();/* 1 => question && 2 => quiz */
            $table->text("marketing_tag")->nullable();
            $table->text("conversion_tag")->nullable();
        });
    }

    public function down() {
        Schema::dropIfExists('itemgoogles');
    }
};
