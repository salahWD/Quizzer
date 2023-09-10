<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model {
    use HasFactory;


  const fontFamilies = [
    ["id" => 1, "name" => "tahoma"],
    ["id" => 2, "name" => "google fonts"],
    ["id" => 3, "name" => "arial"]
  ];

  const Default = [
    "font_family" => "1",
    "main_text_color" => "#222222",
    "background_color" => "#ffffff",
    "btn_color" => "#329dcd",
    "btn_text_color" => "#ffffff",
    "border_color" => "#dde5eb",
    "highlight_color" => "#329dcd",
    "answer_bg_color" => "#f5f8fa",
    "answer_text_color" => "#222222",
    "result_btn_color" => "#329dcd",
    "result_btn_text_color" => "#ffffff",
    "image_opacity" => "100",
    "image" => null,
  ];

  public static function public_themes() {
    return Theme::where("is_public", 1)->get();
  }

}
