<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use \Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Http\Request;
use App\Models\Answer;
use App\Models\Quiz;
use App\Models\Theme;
use App\Models\Website;
use App\Models\Question;
use App\Models\Result;
use App\Models\Submission;
use File;

class QuizController extends Controller {

  public function __construct() {
    $this->middleware('auth')->except("show");
  }

  public function index(Website $website) {

    return view("quizzes.index")->with(compact('website'));
  }

  public function create(Website $website, Quiz $quiz) {

    return view("quizzes.create")->with(compact('website'));
  }

  public function store(Website $website, Request $request) {

    $validated = $request->validate([
      'name' => 'required|max:255',
      'type' => 'required',
    ]);

    $quiz = Quiz::create([
      "type" => Quiz::get_type_id(request("type")),// Quiz Constant (LogicsTypes)
      "website_id" => $website->id,
      "en" => [
        "name" => request("name"),
        "intro_title" => "",
        "intro_description" => "",
        "intro_btn" => "",
        "policy_label" => "",
      ],
    ]);

    return redirect()->route('build_quiz', compact("quiz"));

  }

  public function show(Quiz $quiz) {

    if ($quiz->status == 1 && $quiz->can_open_quiz()) {

      $quiz->add_view();

      $questions = Question::with("translations")
          ->with("integrations")
          ->orderBy("questions.order")
          ->where("quiz_id", $quiz->id)
          ->get();

      $results = Result::with("translations")->where("quiz_id", $quiz->id)->get();

      $lang = app()->getLocale();

      $quiz->translate($lang);// to get translated info

      return view("quizzes.taking-quiz")->with(compact("quiz", "questions", "results", "lang"));

    }else {
      abort(404);
    }

  }

  public function build($quiz_id) {

    $quiz = Quiz::where("id", $quiz_id)->with("translations")->get()->first();

    $website = Website::where("id", $quiz->website_id)->with("integrations")->get()->first();

    $question = new Question();
    $questions_types = $question->types();

    $result = new Result();
    $results_types = $result->types();

    $langResults = app()->getLocale() . "_results";
    $results = $quiz->$langResults();

    $langQuestions = app()->getLocale() . "_questions";
    $questions = $quiz->$langQuestions();

    $fonts = Theme::fontFamilies;
    $formRoute = route("update_quiz", compact("website", "quiz"));
    // $quiz->translate(app()->getLocale());

    return view("quizzes.builder")->with(compact("formRoute", "quiz", "website", "questions_types", "questions", "results_types", "results", "fonts"));
  }

  public function design(Website $website, Quiz $quiz) {

    $themes = Theme::public_themes();
    $fonts = Theme::fontFamilies;

    return view("quizzes.design")->with(compact("website", "quiz", "themes", "fonts"));
  }

  public function delete_image(Quiz $quiz, Request $request) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {

      if ($quiz->image != null && File::exists(public_path("images/uploads/$quiz->image"))) {
        File::delete(public_path("images/uploads/$quiz->image"));
      }
      $quiz->image = null;
      return $quiz->save();
    }
    return false;

  }

  public function delete_intro_image(Quiz $quiz, Request $request) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {

      if ($quiz->intro_image != null && File::exists(public_path("images/uploads/$quiz->intro_image"))) {
        File::delete(public_path("images/uploads/$quiz->intro_image"));
      }
      $quiz->intro_image = null;
      $quiz->save();
    }
    return redirect()->route("build_quiz", $quiz->id);

  }

  public function update_design(Website $website, Quiz $quiz, Request $request) {

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
    return redirect()->route("design_quiz", compact("quiz"));

    return true;

  }

  public function update_translate(Quiz $quiz, Request $request) {

    $info = 0;

    $validated = $request->validate([
      "lang"            => "string|in:ar,en",
      "results"         => "array",
      "results.*.id"    => "required|exists:results,id",
      "results.*.type"  => "required|integer|min:1|max:5",
      "results.*.title" => "required|string|max:200",
      "results.*.desc"  => "nullable|sometimes|string|max:400",
      "results.*.button_label" => "required_if:type,1|string|max:400",
      "questions" => "array|min:1",
      "questions.*.id" => "required|exists:questions",
      "questions.*.type" => "required|integer|min:1|max:5",
      "questions.*.title" => "required|string|max:200",
      "questions.*.desc" => "nullable|sometimes|string|max:400",
      "questions.*.button_label" => "required_if:type,3,4,5|string|max:400",
      "questions.*.answers" => "nullable|array",
      "questions.*.answers.*.id" => "exists:answers",
      "questions.*.answers.*.text" => "sometimes|string",
    ]);

    $questions_of_quiz = Question::where('quiz_id', $quiz->id)->pluck('id')->toArray();
    $results_of_quiz = Result::where('quiz_id', $quiz->id)->pluck('id')->toArray();

    $saveable_questions = [];

    foreach (request("questions") as $question) {
      if (in_array($question["id"], $questions_of_quiz)) {
        $question_model = Question::get()->where("id", $question["id"])->first();
        $translate = $question_model->translateOrNew(request("lang"));

        $translate->title = $question["title"];
        $translate->description = $question["desc"];

        if (in_array($question["type"], [1, 2]) && is_array($question["answers"]) && count($question["answers"]) > 0) {
          foreach ($question["answers"] as $answer) {
            $info++;
            DB::table("answer_translations")
                ->updateOrInsert(
                ["answer_id" => $answer["id"], 'locale' => request("lang")],
                ["text" => $answer["text"]]
              );
          }
        }else{
          $translate->button_label = $question["button_label"];
        }

        $saveable_questions[] = $question_model;

      }else {
        return false;
      }
    }

    $quiz->questions()->saveMany($saveable_questions);

    // ============ Result ============ //

    if (count(request("results")) > 0) {

      $saveable_results = [];

      foreach (request("results") as $result) {
        if (in_array($result["id"], $results_of_quiz)) {
          $result_model = Result::get()->where("id", $result["id"])->first();
          $translate = $result_model->translateOrNew(request("lang"));

          $translate->title = $result["title"];
          $translate->description = $result["desc"];

          if (isset($result["button_label"])) {
            $translate->button_label = $result["button_label"];
          }

          $saveable_results[] = $result_model;

        }else {
          return false;
        }
      }

      $quiz->results()->saveMany($saveable_results);

    }

    // redirect to next page
    return redirect()->route("translate_quiz", compact("quiz"))->with(["info" => $info]);
  }

  public function select_theme(Website $website, Quiz $quiz, Request $request) {

    $validated = $request->validate([
      "theme_id" => "required|numeric",
    ]);

    return $request->theme_id;


    // get theme info

    // assign theme info to quiz instance

    // save quiz instance

    // redirect to next page

  }

  public function share(Website $website, Quiz $quiz) {

    return view("quizzes.share")->with(compact("website", "quiz"));
  }

  public function config(Quiz $quiz) {

    $integs = config("integrations.INTEGRATIONS") ?? [];
    $integrations = [];
    if (count($integs) > 0) {
      foreach($integs as $integ) {
        if($integ['IS_USABLE']) {
          if ($integ["INTEGRATION"] == "facebook") {
            $fb = $quiz->integrations($integ['INTEGRATION']);
            if ($fb != null && $fb->pivot != null && $fb->pivot->value != null) {
              $integrations["fb"]['custom_event_name'] = $fb->pivot->value;
            }
          }elseif ($integ["INTEGRATION"] == "activeCompaign") {
            $integrations["ac"]['ac_info']      = $quiz->integration_ac_get_lists();
            $integrations["ac"]['active_list']  = $quiz->integrations($integ['INTEGRATION']);
            $integrations['ac']['field_values'] = collect($quiz->integration_ac_get_field_options())->keyBy('ac_value_id');
            if ($integrations["ac"]['active_list'] != null && $integrations["ac"]['active_list']->pivot != null && $integrations["ac"]['active_list']->pivot->value != null) {
              $integrations["ac"]['fields'] = $quiz->integration_ac_get_list_fields($integrations["ac"]['active_list']->pivot->value);
              $values = $quiz->integration_ac;// ac integrations (fields values DB)
              $types = ["Quiz", "Question", "Result", "Field", "Date"];
              $fields_types = ["First-Name" => 3, "Email" => 3, "Last-Name" => 3, "Phone" => 3];
              if ($values->count() > 0) {
                foreach ($integrations["ac"]["fields"] as $field) {
                  $done = false;
                  foreach ($values as $value) {
                    if (!$done && $value->type == $field->id) {
                      if (isset($integrations['ac']['field_values']->get($value->value)->ac_value_title)) {
                        $field->value = $integrations['ac']['field_values']->get($value->value)->ac_value_title;
                      }
                      $field->value_type = $value->value_type;
                      $done = true;
                    }
                  }
                }
              }
            }
          }
        }
      }
    }

    return view("quizzes.config")->with([
      'quiz' => $quiz,
      'integs' => $integrations
    ]);
  }

  public function update_config(Website $website, Quiz $quiz, Request $request) {

    $validation_rules = [
      'meta_title'        => 'nullable',
      'meta_description'  => 'nullable',
      'policy_label'      => 'nullable',
      'policy_link'       => 'nullable|url',
    ];

    $validated = $request->validate($validation_rules);

    $show_logo = request("show_logo") ?? "off";
    $quiz->show_logo = $show_logo == "on" ? 1: 0;

    $quiz->meta_title       = request("meta_title");
    $quiz->meta_description = request("meta_description");
    $quiz->policy_label     = request("policy_label");
    $quiz->policy_link      = request("policy_link");

    $quiz->save();

    return redirect()->route("config_quiz", compact("quiz"));

  }

  public function update_status(Quiz $quiz, Request $request) {

    if (auth()->user()->is_admin() || $quiz->website->user->id == auth()->user()->id) {

      $validated = $request->validate([
        'publish' => 'required|integer|in:1,0',
      ]);

      $quiz->status = request("publish");

      return $quiz->save();

    }else {
      return false;
    }

  }

  public function report($quiz_id) {

    $questions = Question::where("quiz_id", $quiz_id)
        ->select(["questions.*"/* , "en.title AS en_title", "ar.title AS ar_title" */])
        // ->join("question_translations AS en", "en.question_id", "=", DB::raw("questions.id AND en.locale = 'en'"))
        // ->leftJoin("question_translations AS ar", "ar.question_id", "=", DB::raw("questions.id AND ar.locale = 'ar'"))
        ->withCount("entries")
        ->with("answers")
        ->get();

    $quiz = Quiz::where("id", $quiz_id)
        ->with("translations")
        ->withCount("submissions")
        ->get()->first();


    return view("quizzes.report")->with(compact("quiz", "questions"));
  }

  public function export_submissions(Quiz $quiz, Request $request) {

    dd([
      $request["start_date"],
      $request["end_date"]
    ]);

  }

  public function get_submission($submission_id, Request $request) {

    $submission = Submission::with("entries_with_questions_answers")
    ->where("id", $submission_id)
    ->get()->first();

    if ($submission->entries_with_questions_answers->count() > 0) {
      foreach ($submission->entries_with_questions_answers as $entry) {
        if (count($entry->answers_report) > 0) {
          $entry->answers_report;
        }
        if (count($entry->fields_label_value) > 0) {
          $entry->fields_label_value;
        }
      }
    }

    return $submission;

  }

  public function update(Website $website, Quiz $quiz, Request $request) {

    $request->validate([
      'en_intro_title' => 'required|string|max:255',
      'en_intro_btn' => 'required|string|max:20',
      'quiz-intro-img' => 'sometimes|nullable|image|mimes:png,jpg,jpeg|max:2048',// max = 2 mega byte
      'quiz-intro-url' => 'sometimes|nullable|url',
      'show_policy' => 'sometimes|required',
      'en_template_desc' => 'sometimes|nullable|max:255',
    ]);

    if (auth()->user()->can_translate()) {

      $request->validate([
        'ar_intro_title' => 'sometimes|max:255',
        'ar_intro_btn' => 'sometimes|max:20',
        'ar_template_desc' => 'sometimes|nullable|max:255',
      ]);

      foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties) {
        $quiz->translateOrNew($localeCode)->intro_title = request($localeCode . '_intro_title');
        $quiz->translateOrNew($localeCode)->intro_description = request($localeCode . '_intro_description');
        $quiz->translateOrNew($localeCode)->intro_btn = request($localeCode . '_intro_btn');
        if ($quiz->is_template == 1) {
          $quiz->translateOrNew($localeCode)->template_desc = request($localeCode . 'template_desc');
        }
      }

    }else {

      $quiz->intro_title        = request('en_intro_title');
      $quiz->intro_description  = request('en_intro_description');
      $quiz->intro_btn          = request('en_intro_btn');
      if ($quiz->is_template == 1) {
        $quiz->template_desc = request('en_template_desc');
      }
    }

    if (request('quiz-intro-img') != null) {
      $image = request('quiz-intro-img');
      if ($quiz->intro_image != NULL && File::exists(public_path("images/uploads/$quiz->intro_image"))) {
        File::delete(public_path("images/uploads/$quiz->intro_image"));
      }
      $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . $image->getClientOriginalName());
      $image->move(public_path('images/uploads'), $unique_name);
      $quiz->intro_image = $unique_name;
    }
    if (request('quiz-intro-url') != null) {
      $quiz->intro_image = request('quiz-intro-url');
    }

    if ($quiz->policy_link != null) {
      $quiz->is_shown_policy = request('show_policy') == "on" ? 1 : 0;
    }

    $quiz->save();
    return redirect()->route("build_quiz", compact("quiz"));

  }

  public function destroy(Quiz $quiz) {

    $web = $quiz->website;
    if (auth()->user()->is_admin() || auth()->user()->id == $web->user_id) {

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

      return redirect()->route('show_website', $web->id);

    }else {
      return false;
    }

  }

}
