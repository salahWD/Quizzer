<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up() {
    Schema::create('quizzes', function (Blueprint $table) {
        $table->id();
        $table->foreignId("website_id")->nullable();
        $table->foreign('website_id')->references('id')->on('websites')->onDelete('cascade');
        $table->integer("type");// 1 => scoring | 2 => outcome
        $table->integer("views")->default(0);
        $table->string("intro_image")->nullable();
        $table->string("image")->nullable();
        $table->boolean("status")->default(FALSE);
        $table->boolean("is_template")->default(FALSE);
        $table->boolean("is_shown_policy")->default(FALSE);
        $table->tinyinteger("font_family")->default(1);
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
        $table->tinyinteger("image_opacity")->default(100);
        $table->string("meta_title")->nullable();
        $table->string("meta_description")->nullable();
        $table->boolean("show_logo")->default(false);
        $table->string("policy_link")->nullable();
        $table->timestamps();
    });
  }

  public function down() {
      Schema::dropIfExists('quizzes');
  }
};
