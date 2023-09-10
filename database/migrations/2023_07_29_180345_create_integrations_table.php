<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {


    public function up() {
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('website_id');
            $table->string("name");
            $table->string("email")->nullable();
            $table->text("key")->nullable();
            $table->string("url")->nullable();
            $table->unique(['website_id', 'name']);
            $table->foreign('website_id')->references('id')->on('websites')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('integrations');
    }
};
