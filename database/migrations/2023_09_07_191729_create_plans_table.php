<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up() {
      Schema::create('plans', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug');
        $table->string('stripe_plan');
        $table->string('paypal_plan');
        $table->integer('price');
        $table->boolean('best_seller')->nullable();
        $table->string('description');
        $table->timestamps();
      });
    }

    public function down(){
      Schema::dropIfExists('plans');
    }
};
