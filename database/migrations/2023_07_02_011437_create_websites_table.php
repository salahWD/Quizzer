<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {


    public function up() {
        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("user_id")->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string("custom_domain")->nullable();
            $table->string("url");
            $table->string("company");
            $table->boolean("show_watermark")->default(1);
            $table->string("logo_image")->nullable();
            $table->integer("color")->default(1);
            $table->timestamps();
        });
    }


    public function down() {
        Schema::dropIfExists('websites');
    }
};
