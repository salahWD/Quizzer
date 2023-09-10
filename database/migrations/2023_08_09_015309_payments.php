<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up() {
    Schema::create('payments', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('user_id');
      $table->string('payment_id')->nullable();
      $table->enum('status', ["pending", "completed"])->default("pending");
      $table->double('amount', 8, 2);
      $table->tinyInteger('package_id');
      $table->enum('method', ["paypal", "stripe", "bank"]);
      $table->string('bank_bill')->nullable();
      $table->boolean('is_annual')->default(0);
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->timestamps();
    });
  }

  public function down() {
    Schema::dropIfExists('payments');
  }

};
