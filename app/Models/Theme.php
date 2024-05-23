<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model {
    use HasFactory;

  const ArabicFontFamilies = [
    ["id" => 0, "name" => "Rubik", "url" => "https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"],
    ["id" => 1, "name" => "Tajawal", "url" => "https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap"],
    ["id" => 2, "name" => "Almarai", "url" => "https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap"],
    ["id" => 3, "name" => "Amiri", "url" => "https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400;1,700&display=swap"],
    ["id" => 4, "name" => "El Messiri", "url" => "https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;500;600;700&display=swap"],
    ["id" => 5, "name" => "Cairo", "url" => "https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900;1000&display=swap"],
    ["id" => 6, "name" => "Katibeh", "url" => "https://fonts.googleapis.com/css2?family=Katibeh&display=swap"],
    ["id" => 7, "name" => "Noto Kufi Arabic", "url" => "https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100;200;300;400;500;600;700;800;900&display=swap"],
    ["id" => 8, "name" => "Reem Kufi Fun", "url" => "https://fonts.googleapis.com/css2?family=Reem+Kufi+Fun:wght@400;500;600;700&display=swap"],
    ["id" => 9, "name" => "Gulzar", "url" => "https://fonts.googleapis.com/css2?family=Gulzar&display=swap"],
    ["id" => 10, "name" => "Baloo Bhaijaan 2", "url" => "https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2:wght@400;500;600;700;800&display=swap"],
    ["id" => 11, "name" => "Aref Ruqaa", "url" => "https://fonts.googleapis.com/css2?family=Aref+Ruqaa:wght@400;700&display=swap"],
  ];

  const fontFamilies = [
    ["id" => 0, "name" => "Jost", "url" => "https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"],
    ["id" => 1, "name" => "Roboto", "url" => "https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"],
    ["id" => 2, "name" => "Open Sans", "url" => "https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"],
    ["id" => 3, "name" => "Poppins", "url" => "https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"],
    ["id" => 4, "name" => "Lato", "url" => "https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"],
    ["id" => 5, "name" => "Roboto Condensed", "url" => "https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"],
    ["id" => 6, "name" => "Montserrat", "url" => "https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"],
    ["id" => 7, "name" => "Ubuntu", "url" => "https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap"],
    ["id" => 8, "name" => "PT Serif", "url" => "https://fonts.googleapis.com/css2?family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap"],
    ["id" => 9, "name" => "Inter", "url" => "https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap"],
    ["id" => 10, "name" => "Oswald", "url" => "https://fonts.googleapis.com/css2?family=Oswald:wght@200;300;400;500;600;700&display=swap"],
    ["id" => 11, "name" => "Raleway", "url" => "https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"],
  ];

  const Default = [
    "is_public" => "1",
    "font_family" => "0",
    "font_family_ar" => "0",
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

  public static function create_default_theme() {
    $theme = new Theme();
    foreach (SELF::Default as $key => $value) {
      $theme->$key = $value;
    }
    return $theme;
  }

  public static function public_themes() {
    return Theme::where("is_public", 1)->get();
  }

}
