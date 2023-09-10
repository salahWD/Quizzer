<?php

namespace App\Http\Controllers;

use File;
use Illuminate\Support\Facades\DB;
use \Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Theme;
use App\Models\Website;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Result;
use App\Models\Condition;

class TemplateController extends Controller {

    public function __construct() {
      $this->middleware('auth')->except(['preview', 'show']);
    }

    public function copy(Website $website, $quiz_id, Request $request) {

      if ($website->user->id == auth()->user()->id) {

        $template_info = Quiz::where("id", $quiz_id)
            ->with("translated_questions")
            // ->with("results")
            ->with("translations")
            ->get()
            ->first();

        $copy = $template_info->replicateWithTranslations();

        if (!auth()->user()->is_admin() && !auth()->user()->can_translate()) {
          foreach ($copy->translations as $key => $trans) {
            if ($trans->locale != "en") {
              $copy->translations->forget($key);
            }
          }
        }

        $copy->template_desc = "";
        $copy->is_template   = 0;
        $copy->views         = 0;
        $copy->status        = 0;
        $copy->website_id    = $website->id;

        if ($template_info->image != NULL && File::exists(public_path("images/uploads/$template_info->image"))) {
          $unique_str = date('mdYHis') . uniqid();
          $new_image_name = $unique_str . substr($template_info->image, strlen($unique_str));
          File::copy(public_path("images/uploads/$template_info->image") , public_path("images/uploads/$new_image_name"));
          $copy->image = $new_image_name;
        }

        if ($template_info->intro_image != NULL && File::exists(public_path("images/uploads/$template_info->intro_image"))) {
          $unique_str = date('mdYHis') . uniqid();
          $new_image_name = $unique_str . substr($template_info->intro_image, strlen($unique_str));
          File::copy(public_path("images/uploads/$template_info->intro_image") , public_path("images/uploads/$new_image_name"));
          $copy->intro_image = $new_image_name;
        }

        $copy->save();

        /* ====== replicate results ====== */
        if ($template_info->translated_results->count() > 0) {
          $results_ids_map = [];
          foreach ($template_info->translated_results as $i => $result) {

            $old_id = $result->id;

            $newResult = $result->replicateWithTranslations();

            /* ====== currect translations ====== */
            if (!auth()->user()->is_admin() && !auth()->user()->can_translate()) {
              foreach ($newResult->translations as $key => $trans) {
                if ($trans->locale != "en") {
                  $newResult->translations->forget($key);
                }
              }
            }

            $newResult->quiz_id = $copy->id;
            $newResult->save();

            $results_ids_map[$old_id] = $newResult->id;

          }
        }

        /* ====== replicate questions ====== */
        if ($template_info->translated_questions->count() > 0) {

          $questions_ids_map = [];
          $answers_ids_map = [];

          foreach ($template_info->translated_questions as $question) {

            $copyQuestion = $question->replicateWithTranslations();

            /* ====== currect translations ====== */
            if (!auth()->user()->is_admin() && !auth()->user()->can_translate()) {
              foreach ($copyQuestion->translations as $key => $trans) {
                if ($trans->locale != "en") {
                  // $post->deleteTranslations($trans->locale)
                  $copyQuestion->translations->forget($key);
                }
              }
            }

            /* ====== copy questino image ====== */
            if (in_array($copyQuestion->type, [1, 2, 3, 5])) {

              if ($copyQuestion->image != NULL && File::exists(public_path("images/uploads/$copyQuestion->image"))) {
                $unique_str = date('mdYHis') . uniqid();
                $new_image_name = $unique_str . substr($copyQuestion->image, strlen($unique_str));
                File::copy(public_path("images/uploads/$copyQuestion->image") , public_path("images/uploads/$new_image_name"));
                $copyQuestion->image = $new_image_name;
              }
            }

            $copyQuestion->quiz_id = $copy->id;
            $copyQuestion->views = 0;

            $copyQuestion->save();
            $questions_ids_map[$question->id] = $copyQuestion->id;

            /* ====== replicate answers ====== */
            if (in_array($question->type, [1, 2])) {
              if ($question->translated_answers->count() > 0) {

                foreach ($question->translated_answers as $answer) {

                  $duplicatedAnswer = $answer->replicateWithTranslations();

                  // get currect translation
                  if (!auth()->user()->is_admin() && !auth()->user()->can_translate()) {
                    foreach ($answer->translations as $key => $trans) {
                      if ($trans->locale != "en") {
                        $answer->translations->forget($key);
                      }
                    }
                  }

                  if ($question->type == 2) {
                    if ($duplicatedAnswer->image != NULL && File::exists(public_path("images/uploads/$duplicatedAnswer->image"))) {
                      $unique_str = date('mdYHis') . uniqid();
                      $new_image_name = $unique_str . substr($duplicatedAnswer->image, strlen($unique_str));
                      File::copy(public_path("images/uploads/$duplicatedAnswer->image") , public_path("images/uploads/$new_image_name"));
                      $duplicatedAnswer->image = $new_image_name;
                    }
                  }

                  $duplicatedAnswer->question_id = $copyQuestion->id;

                  $duplicatedAnswer->save();

                  $answers_ids_map[$answer->id] = $duplicatedAnswer->id;

                  if ($template_info->type == 2 && isset($results_ids_map) && !empty($results_ids_map)) {

                    $answersArray = [];
                    foreach ($answer->results->pluck("id") as $res_id) {
                      $answersArray[] = $results_ids_map[$res_id];
                    }

                    $duplicatedAnswer->results()->sync($answersArray);
                  }

                  // $answers[] = $duplicatedAnswer;
                }

                // $copyQuestion->answers()->saveMany($answers);

              }
            }

          }

        }

        $conditions = Condition::with("answers")
            ->select("conditions.*")
            ->join("questions", "questions.id", "=", "conditions.question_id")
            ->join("quizzes", "quizzes.id", "=", "questions.quiz_id")
            ->where("quizzes.id", $template_info->id)
            ->get();

        if ($conditions->count() > 0 && !empty($questions_ids_map)) {
          foreach ($conditions as $cond) {

            //  if condition is not there it will accure an error

            $newCond = $cond->replicate();
            $newCond->question_id = $questions_ids_map[$cond->question_id];
            $newCond->target_id = ($cond->target_type > 5) ? $results_ids_map[$cond->target_id] : $questions_ids_map[$cond->target_id];
            $newCond->save();
            if ($cond->answers->count() > 0) {
              $syncAnsrs = [];
              foreach ($cond->answers as $ansr) {
                $syncAnsrs[] = $answers_ids_map[$ansr->id];
              }
              $newCond->answers()->sync($syncAnsrs);
            }
            // $results_ids_map
            // $questions_ids_map
            // $answers_ids_map

          }
        }

        return redirect()->route("build_quiz", $copy->id);

      }else {
        return abort(404);
      }

    }

    public function create() {
      if (auth()->user()->is_admin()) {
        return view("templates.create_template");
      }else {
        return abort(404);
      }
    }

    public function store(Request $request) {

      if (auth()->user()->is_admin()) {

        $validated = $request->validate([
          'name' => 'required|max:255',
          'en_template_desc' => 'required|max:255',
          'type' => 'required|in:1,2',
        ]);

        $quiz = Quiz::create([
          "is_template" => 1,
          "type" => request("type"),
          "en" => [
            "name" => request("name"),
            "template_desc" => request("en_template_desc"),
            "intro_title" => "",
            "intro_description" => "",
            "intro_btn" => "",
            "policy_label" => "",
          ],
        ]);

        return redirect()->route('edit_template', compact("quiz"));

      }else {
        return false;
      }

    }

    public function show() {

      $templates = Quiz::where("is_template", 1)->with("translations")->get();

      if ($templates->count() > 0) {
        return view("templates.show_template", compact("templates"));
      }
      return abort(404);

    }

    public function preview(Quiz $quiz) {

      if ($quiz->status == 1) {

        $quiz->add_view();

        $lcaleQuestions = app()->getLocale() == "ar" ? "ar_questions" : "langs_questions";
        if ($quiz->type == 1) {
          $lcaleResults = app()->getLocale() == "ar" ? "ar_score_results" : "langs_score_results";
        }else {
          $lcaleResults = app()->getLocale() == "ar" ? "ar_outcome_results" : "langs_outcome_results";
        }

        $questions = $quiz->$lcaleQuestions();
        $results = $quiz->$lcaleResults();

        $lang = app()->getLocale();

        $quiz->translate($lang);

        return view("quizzes.taking-quiz")->with(compact("quiz", "questions", "results", "lang"));

      }else {
        abort(404);
      }
    }

    public function theme(Quiz $quiz) {

      if (auth()->user()->is_admin()) {

        $themes = Theme::public_themes();
        $fonts = Theme::fontFamilies;

        return view("templates.edit_theme")->with(compact("quiz", "themes", "fonts"));

      }

    }

    public function update_theme(Quiz $quiz, Request $request) {

      if (auth()->user()->is_admin()) {

        $validated = $request->validate([
          "font_family"             => "required|numeric",
          "main_text_color"         => "required|max:7|starts_with:#",
          "background_color"        => "required|max:7|starts_with:#",
          "btn_color"               => "required|max:7|starts_with:#",
          "btn_text_color"          => "required|max:7|starts_with:#",
          "border_color"            => "required|max:7|starts_with:#",
          "highlight_color"         => "required|max:7|starts_with:#",
          "answer_bg_color"         => "required|max:7|starts_with:#",
          "answer_text_color"       => "required|max:7|starts_with:#",
          "result_btn_color"        => "required|max:7|starts_with:#",
          "result_btn_text_color"   => "required|max:7|starts_with:#",
          "image"                   => "nullable|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte
          "image_opacity"           => "required|numeric|min:0|max:100",
        ]);

        // set design info to quiz instance
        $quiz->font_family            = request("font_family");
        $quiz->main_text_color        = request("main_text_color");
        $quiz->background_color       = request("background_color");
        $quiz->btn_color              = request("btn_color");
        $quiz->btn_text_color         = request("btn_text_color");
        $quiz->border_color           = request("border_color");
        $quiz->highlight_color        = request("highlight_color");
        $quiz->answer_bg_color        = request("answer_bg_color");
        $quiz->answer_text_color      = request("answer_text_color");
        $quiz->result_btn_color       = request("result_btn_color");
        $quiz->result_btn_text_color  = request("result_btn_text_color");
        $quiz->image_opacity          = request("image_opacity");

        if ($request->hasFile('image')) {
          $quiz->image = date('mdYHis') . uniqid() . $request->file('image')->getClientOriginalName();
          $request->image->move(public_path('images/uploads'), $quiz->image);
        }

        // save quiz instance
        $quiz->save();

        // redirect to next page
        return redirect()->route("template_theme", compact("quiz"));

        return true;

      }else {
        return false;
      }

    }

    public function select_theme(Quiz $quiz, Request $request) {

      if (auth()->user()->is_admin()) {

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
        return redirect()->route("update_template_theme", $quiz->id);

      }else {
        return abort(404);
      }

    }

    public function edit(Quiz $quiz) {

      if (auth()->user()->is_admin()) {

        $question = new Question();
        $questions_types = $question->types();

        $result = new Result();
        $results_types = $result->types();

        $langResults = app()->getLocale() . "_results";
        $results = $quiz->$langResults();

        $langQuestions = app()->getLocale() . "_questions";
        $questions = $quiz->$langQuestions();

        $fonts = Theme::fontFamilies;

        $formRoute = route("update_template", compact("quiz"));

        return view("quizzes.builder")->with(compact("formRoute", "quiz", "questions_types", "questions", "results_types", "results", "fonts"));

      }

    }

    public function update(Request $request, Quiz $quiz) {

      if (auth()->user()->is_admin()) {

        $request->validate([
          'en_intro_title' => 'required|max:255',
          'en_intro_btn' => 'required|max:20',
        ]);

        if (auth()->user()->is_sub() || auth()->user()->is_admin()) {

          $request->validate([
            'ar_template_desc' => 'sometimes|max:255',
            'ar_intro_title' => 'sometimes|max:255',
            'ar_intro_btn' => 'sometimes|max:20',
          ]);

          foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties) {
            $quiz->translateOrNew($localeCode)->template_desc = request($localeCode . '_template_desc');
            $quiz->translateOrNew($localeCode)->intro_title = request($localeCode . '_intro_title');
            $quiz->translateOrNew($localeCode)->intro_description = request($localeCode . '_intro_description');
            $quiz->translateOrNew($localeCode)->intro_btn = request($localeCode . '_intro_btn');
          }

        }else {

          $quiz->template_desc      = request('en_template_desc');
          $quiz->intro_title        = request('en_intro_title');
          $quiz->intro_description  = request('en_intro_description');
          $quiz->intro_btn          = request('en_intro_btn');

        }

        $quiz->save();
        return redirect()->route("edit_template", compact("quiz"));

      }

    }

    public function destroy(Quiz $quiz) {

      if (auth()->user()->is_admin()) {

        $quiz_images = Quiz::select("id", "image", "intro_image")
            ->where("id", $quiz->id)
            ->get()->first();

        $questions_images = Question::select("questions.id", "questions.image")
            ->where("quiz_id", $quiz->id)
            ->whereNotNull("image")
            ->get();

        $results_images = Answer::select("answers.id", "answers.image")
            ->join("questions", "questions.id", "=", "answers.question_id")
            ->where("questions.quiz_id", $quiz->id)
            ->whereNotNull("answers.image")
            ->get();

        if ($quiz_images->image != null && File::exists(public_path("images/uploads/$quiz_images->image"))) {
          File::delete(public_path("images/uploads/$quiz_images->image"));
        }
        if ($quiz_images->intro_image != null && File::exists(public_path("images/uploads/$quiz_images->intro_image"))) {
          File::delete(public_path("images/uploads/$quiz_images->intro_image"));
        }
        foreach ([...$results_images, ...$questions_images] as $image) {
          if ($image->image != null && File::exists(public_path("images/uploads/$image->image"))) {
            File::delete(public_path("images/uploads/$image->image"));
          }
        }

        $quiz->delete();

        return redirect()->route('show_template');

      }else {
        return false;
      }

    }
}

