<?php

namespace App\Http\Controllers;

use File;
use App\Models\Theme;
use App\Models\Quiz;
use Illuminate\Http\Request;

class ThemeController extends Controller {

    public function __construct() {
      $this->middleware(['auth', 'is_admin']);
    }

    public function create() {

      $themes = Theme::all();
      $default = Theme::Default;
      $fonts = Theme::fontFamilies;

      return view("themes.create", compact("themes", "default", "fonts"));

    }

    public function store(Request $request) {

      if (auth()->user()->status == "admin") {

        $request->validate([
          "is_public"             => "sometimes|numeric|in:1,0",
          "font_family"           => "required|numeric",
          "main_text_color"       => "required|max:7|starts_with:#",
          "background_color"      => "required|max:7|starts_with:#",
          "btn_color"             => "required|max:7|starts_with:#",
          "btn_text_color"        => "required|max:7|starts_with:#",
          "border_color"          => "required|max:7|starts_with:#",
          "highlight_color"       => "required|max:7|starts_with:#",
          "answer_bg_color"       => "required|max:7|starts_with:#",
          "answer_text_color"     => "required|max:7|starts_with:#",
          "result_btn_color"      => "required|max:7|starts_with:#",
          "result_btn_text_color" => "required|max:7|starts_with:#",
          "image"                 => "nullable|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte
          "image_opacity"         => "required|numeric|min:0|max:100",
        ]);

        $theme = new Theme();

        // set Theme info
        $theme->is_public             = request("is_public") ?? 0;
        $theme->font_family           = request("font_family");
        $theme->main_text_color       = request("main_text_color");
        $theme->background_color      = request("background_color");
        $theme->btn_color             = request("btn_color");
        $theme->btn_text_color        = request("btn_text_color");
        $theme->border_color          = request("border_color");
        $theme->highlight_color       = request("highlight_color");
        $theme->answer_bg_color       = request("answer_bg_color");
        $theme->answer_text_color     = request("answer_text_color");
        $theme->result_btn_color      = request("result_btn_color");
        $theme->result_btn_text_color = request("result_btn_text_color");
        $theme->image_opacity         = request("image_opacity");

        if ($request->hasFile('image')) {
          $theme->image = date('mdYHis') . uniqid() . $request->file('image')->getClientOriginalName();
          $request->image->move(public_path('images/uploads'), $theme->image);
        }

        // save quiz instance
        $theme->save();

        // redirect to next page
        return redirect()->route("edit_theme", $theme->id);

        return true;

      }else {
        return abort(404);
      }

    }

    public function edit(Theme $theme, Request $request) {

      $fonts = Theme::fontFamilies;
      $themes = Theme::all();

      return view("themes.update", compact("themes", "theme", "fonts"));

    }

    public function update(Request $request, Theme $theme) {

      if (auth()->user()->status == "admin") {

        $request->validate([
          "id"                => "required|exists:themes",
          "is_public"         => "sometimes|numeric|in:1,0",
          "font_family"       => "required|numeric",
          "main_text_color"   => "required|max:7|starts_with:#",
          "background_color"  => "required|max:7|starts_with:#",
          "btn_color"         => "required|max:7|starts_with:#",
          "btn_text_color"    => "required|max:7|starts_with:#",
          "border_color"      => "required|max:7|starts_with:#",
          "highlight_color"   => "required|max:7|starts_with:#",
          "answer_bg_color"   => "required|max:7|starts_with:#",
          "answer_text_color" => "required|max:7|starts_with:#",
          "result_btn_color"  => "required|max:7|starts_with:#",
          "result_btn_text_color" => "required|max:7|starts_with:#",
          "image"             => "nullable|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte
          "image_opacity"     => "required|numeric|min:0|max:100",
        ]);

        // set Theme info
        $theme->is_public             = request("is_public") ?? 0;
        $theme->font_family           = request("font_family");
        $theme->main_text_color       = request("main_text_color");
        $theme->background_color      = request("background_color");
        $theme->btn_color             = request("btn_color");
        $theme->btn_text_color        = request("btn_text_color");
        $theme->border_color          = request("border_color");
        $theme->highlight_color       = request("highlight_color");
        $theme->answer_bg_color       = request("answer_bg_color");
        $theme->answer_text_color     = request("answer_text_color");
        $theme->result_btn_color      = request("result_btn_color");
        $theme->result_btn_text_color = request("result_btn_text_color");
        $theme->image_opacity         = request("image_opacity");

        if ($request->hasFile('image')) {

          $old_image = $theme->image;
          $new_image_name = date('mdYHis') . uniqid() . $request->file('image')->getClientOriginalName();

          if (File::exists(public_path("images/uploads/") . $old_image)) {
            File::delete(public_path("images/uploads/") . $old_image);
          }
          $request->image->move(public_path('images/uploads'), $new_image_name);
          $theme->image = $new_image_name;

        }

        $theme->save();

        return redirect()->route("edit_theme", $theme->id);

      }else {
        return abort(404);
      }

    }

    public function delete_image(Theme $theme) {

      if (auth()->user()->is_admin()) {

        if ($theme->image != null && File::exists(public_path("images/uploads/$theme->image"))) {
          File::delete(public_path("images/uploads/$theme->image"));
        }
        $theme->image = null;
        return $theme->save();
      }
      return false;
    }

    public function copy(Quiz $quiz, Request $request) {

      if ($quiz->website->user->id == auth()->user()->id) {

        $request->validate([
          "theme_id" => "required|exists:themes,id",
        ]);

        $design_info = Theme::where("id", request("theme_id"))->get()->first();

        // set Theme info
        $quiz->font_family           = $design_info->font_family;
        $quiz->main_text_color       = $design_info->main_text_color;
        $quiz->background_color      = $design_info->background_color;
        $quiz->btn_color             = $design_info->btn_color;
        $quiz->btn_text_color        = $design_info->btn_text_color;
        $quiz->border_color          = $design_info->border_color;
        $quiz->highlight_color       = $design_info->highlight_color;
        $quiz->answer_bg_color       = $design_info->answer_bg_color;
        $quiz->answer_text_color     = $design_info->answer_text_color;
        $quiz->result_btn_color      = $design_info->result_btn_color;
        $quiz->result_btn_text_color = $design_info->result_btn_text_color;
        $quiz->image_opacity         = $design_info->image_opacity;

        if ($design_info->image != null) {

          $quiz->image = str_replace(" ", "", date('mdYHis') . uniqid() . substr($design_info->image, -10));

          File::copy(public_path("images/uploads/$design_info->image") , public_path("images/uploads/$quiz->image"));

        }

        $quiz->save();
        return redirect()->route("design_quiz", $quiz->id);

      }else {
        return abort(404);
      }

    }

    public function destroy(Request $request, Theme $theme) {

      if (auth()->user()->status == "admin") {

        $request->validate([
          "id" => "required|exists:themes",
        ]);

        $theme->id = request("id");

        if (File::exists(public_path("images/uploads/") . $theme->image)) {
          File::delete(public_path("images/uploads/") . $theme->image);
        }

        $theme->delete();

        return redirect()->route("create_theme");

      }else {
        return abort(404);
      }


    }
}
