<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {


    public function up() {
      Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $fields = ["inactive", "level-1", "level-2", "level-3", "admin"];
        $table->enum('status', $fields);
        $table->boolean('took_trial')->default(0);
        $table->timestamps();
        $table->timestamp('subscription_end')->nullable();
        $table->rememberToken();
      });
    }

    public function down() {
      Schema::dropIfExists('users');
    }
};
