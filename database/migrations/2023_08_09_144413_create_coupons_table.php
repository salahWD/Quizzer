<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up() {
    Schema::create('coupons', function (Blueprint $table) {
      $table->id();
      $table->tinyInteger('package_id')->nullable();
      $table->string("code", 10);
      $table->double('amount', 8, 2);
      $table->date("expire_date")->nullable()->default(null);
      $table->unique('code');
      $table->integer('usage')->default(0);
      $table->timestamps();
    });
  }


  public function down() {
    Schema::dropIfExists('coupons');
  }

};
