<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up() {
      Schema::create('themes', function (Blueprint $table) {
        $table->id();
        $table->boolean("is_public")->default(FALSE);
        $table->tinyinteger("font_family")->default(1);
        $table->string("name");
        $table->string("main_text_color", 7)->default("#222222");
        $table->string("background_color", 7)->default("#ffffff");
        $table->string("btn_color", 7)->default("#329dcd");
        $table->string("btn_text_color", 7)->default("#ffffff");
        $table->string("border_color", 7)->default("#dde5eb");
        $table->string("highlight_color", 7)->default("#329dcd");
        $table->string("answer_bg_color", 7)->default("#f5f8fa");
        $table->string("answer_text_color", 7)->default("#222222");
        $table->string("result_btn_color", 7)->default("#329dcd");
        $table->string("result_btn_text_color", 7)->default("#ffffff");
        $table->string("image")->nullable();
        $table->tinyinteger("image_opacity")->default(100);
        $table->timestamps();
      });
    }

    public function down() {
      Schema::dropIfExists('themes');
    }
};
