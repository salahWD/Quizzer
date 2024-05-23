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
use App\Models\Entry;
use App\Models\Option;
use File;
use App\Models\Condition;

class QuizController extends Controller {

  public function __construct() {
    $this->middleware(['auth', 'is_subscribed'])->except("show");
  }

  public function index(Website $website) {

    if (auth()->user()->is_admin() || auth()->user()->id == $website->user_id) {

      return view("quizzes.index")->with(compact('website'));
    } else {
      return abort(404);
    }
  }

  public function create(Website $website, Quiz $quiz) {

    if (auth()->user()->is_admin() || auth()->user()->id == $website->user_id) {

      return view("quizzes.create")->with(compact('website'));
    } else {
      return abort(404);
    }
  }

  public function store(Website $website, Request $request) {

    if (auth()->user()->is_admin() || auth()->user()->id == $website->user_id) {

      $validated = $request->validate([
        'name' => 'required|max:255',
        'type' => 'required',
      ]);

      $quiz = Quiz::create([
        "type" => Quiz::get_type_id(request("type")), // Quiz Constant (LogicsTypes)
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
    } else {
      return abort(404);
    }
  }

  public function show(Quiz $quiz) {

    if ($quiz->status == 1) {

      $submission = Submission::create([
        "quiz_id" => $quiz->id,
      ]);

      $submission_code = $submission->id;

      if ($quiz->activate_utm == 1) {
        if (app('request')->has('utm_source')) :
          $entry = Entry::create([
            'utm_name' => 'utm_source',
            'utm_value' => app('request')->input('utm_source'),
            'submission_id' => $submission->id
          ]);
        endif;

        if (app('request')->has('utm_medium')) :
          $entry = Entry::create([
            'utm_name' => 'utm_medium',
            'utm_value' => app('request')->input('utm_medium'),
            'submission_id' => $submission->id
          ]);
        endif;

        if (app('request')->has('utm_campaign')) :
          $entry = Entry::create([
            'utm_name' => 'utm_campaign',
            'utm_value' => app('request')->input('utm_campaign'),
            'submission_id' => $submission->id
          ]);
        endif;

        if (app('request')->has('utm_id')) :
          $entry = Entry::create([
            'utm_name' => 'utm_id',
            'utm_value' => app('request')->input('utm_id'),
            'submission_id' => $submission->id
          ]);
        endif;

        if (app('request')->has('utm_term')) :
          $entry = Entry::create([
            'utm_name' => 'utm_term',
            'utm_value' => app('request')->input('utm_term'),
            'submission_id' => $submission->id
          ]);
        endif;

        if (app('request')->has('utm_content')) :
          $entry = Entry::create([
            'utm_name' => 'utm_content',
            'utm_value' => app('request')->input('utm_content'),
            'submission_id' => $submission->id
          ]);
        endif;
      }

      $fonts = Theme::fontFamilies;
      if (app()->getLocale() == "ar") {
        $fonts = Theme::ArabicFontFamilies;
      }
      // dd($fonts[$quiz->font_family],$fonts[$quiz->font_family_ar]);

      $quiz->add_view();

      $questions = Question::with("translations")
        ->with("integrations")
        ->orderBy("questions.order")
        ->where("quiz_id", $quiz->id)
        ->get();

      $results = Result::with("translations")
        ->leftJoin("fbitems", "fbitems.item_id", '=', DB::raw("results.id AND fbitems.item_type = 2"))
        ->select("fbitems.*", "results.*")
        ->where("quiz_id", $quiz->id)->get();

      $lang = app()->getLocale();

      $quiz->translate($lang); // to get translated info

      return view("quizzes.taking-quiz")->with(compact("quiz", "questions", "results", "lang", "fonts", "submission_code"));
    } else {
      abort(404);
    }
  }

  public function build($quiz_id) {

    $quiz = Quiz::where("id", $quiz_id)->with("translations")->get()->first();

    $website = Website::where("id", $quiz->website_id)->with("integrations")->get()->first();

    if (auth()->user()->is_admin() || auth()->user()->id == $website->user_id) {

      $question = new Question();
      $questions_types = $question->types();

      $result = new Result();
      $results_types = $result->types();

      $results = $quiz->results()->with("translations")->get();

      $questions = $quiz->questions()->with("translations")->withCount("active_conditions")->get();

      $fonts = Theme::fontFamilies;
      $formRoute = route("update_quiz", compact("quiz"));
      // $quiz->translate(app()->getLocale());

      return view("quizzes.builder")->with(compact("formRoute", "quiz", "website", "questions_types", "questions", "results_types", "results", "fonts"));
    } else {
      return abort(404);
    }
  }

  public function design(Website $website, Quiz $quiz) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {

      $themes = Theme::public_themes();
      $fonts = Theme::fontFamilies;
      $fonts_ar = Theme::ArabicFontFamilies;

      return view("quizzes.design")->with(compact("website", "quiz", "themes", "fonts", "fonts_ar"));
    } else {
      return abort(404);
    }
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

  public function update_design(Quiz $quiz, Request $request) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {

      $validated = $request->validate([
        "font_family_en"          => "required|numeric",
        "font_family_ar"          => "sometimes|nullable|numeric",
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
        "image"                   => "nullable|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
        "image_opacity"           => "required|numeric|min:0|max:100",
      ]);

      // set design info to quiz instance
      $quiz->font_family            = request("font_family_en");
      $quiz->font_family_ar         = request("font_family_ar");
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
      $res = $quiz->save();
      \Session::flash('saved', $res);

      // redirect to next page
      return redirect()->route("design_quiz", compact("quiz"));
    } else {
      return abort(404);
    }

    return true;
  }

  public function update_translate(Quiz $quiz, Request $request) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {

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
          } else {
            $translate->button_label = $question["button_label"];
          }

          $saveable_questions[] = $question_model;
        } else {
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
          } else {
            return false;
          }
        }

        $quiz->results()->saveMany($saveable_results);
      }

      // redirect to next page
      return redirect()->route("translate_quiz", compact("quiz"))->with(["info" => $info]);
    }
  }

  public function select_theme(Website $website, Quiz $quiz, Request $request) {

    if (auth()->user()->is_admin() || auth()->user()->id == $website->user_id) {

      $validated = $request->validate([
        "theme_id" => "required|numeric",
      ]);

      return $request->theme_id;
    } else {
      return abort(404);
    }
  }

  public function share(Website $website, Quiz $quiz) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {
      return view("quizzes.share")->with(compact("website", "quiz"));
    } else {
      return abort(404);
    }
  }

  public function config(Quiz $quiz) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {

      $integs = config("integrations.INTEGRATIONS") ?? [];
      $integrations = [];
      if (count($integs) > 0) {
        $website_integs = $quiz->website->integrations;
        foreach ($integs as $integ) {
          if ($integ['IS_USABLE']) {
            if ($integ["INTEGRATION"] == "facebook") {
              $fbinteg = $website_integs->filter(function ($item) {
                return $item->name == "facebook";
              })->first();
              if ($fbinteg != null && $fbinteg->key != null) {
                $fb = $quiz->integrations($integ['INTEGRATION']);
                $integrations["fb"]['custom_event_name'] = null;
                if ($fb != null && $fb->pivot != null && $fb->pivot->value != null) {
                  $integrations["fb"]['is_used'] = true;
                  $integrations["fb"]['custom_event_name'] = $fb->pivot->value;
                }
              }
            } elseif ($integ["INTEGRATION"] == "tiktok") {
              $tkinteg = $website_integs->filter(function ($item) {
                return $item->name == "tiktok";
              })->first();
              if ($tkinteg != null && $tkinteg->key != null) {
                $tk = $quiz->integrations($integ['INTEGRATION']);
                $integrations["tk"]['custom_event_name'] = null;
                if ($tk != null && $tk->pivot != null && !empty($tk->pivot->value)) {
                  $integrations["tk"]['is_used'] = true;
                  $integrations["tk"]['custom_event_name'] = $tk->pivot->value;
                }
              }
            } elseif ($integ["INTEGRATION"] == "twitter") {
              $twinteg = $website_integs->filter(function ($item) {
                return $item->name == "twitter";
              })->first();
              if ($twinteg != null && $twinteg->key != null) {
                $tw = $quiz->integrations($integ['INTEGRATION']);
                $integrations["tw"]['custom_event_name'] = null;
                if ($tw != null && $tw->pivot != null && !empty($tw->pivot->value)) {
                  $integrations["tw"]['is_used'] = true;
                  $integrations["tw"]['custom_event_name'] = $tw->pivot->value;
                }
              }
            } elseif ($integ["INTEGRATION"] == "snapchat") {

              $scinteg = $website_integs->filter(function ($item) {
                return $item->name == "snapchat";
              })->first();
              if ($scinteg != null && $scinteg->key != null) {
                $integrations['sc'] = ["is_used" => null];
                $quiz_sc = $quiz->integrations("snapchat");
                if ($quiz_sc != null && $quiz_sc->pivot != null && $quiz_sc->pivot->value != null) {
                  $integrations['sc']['is_used'] = $quiz_sc->pivot->value == 1;
                }
              }
            } elseif ($integ["INTEGRATION"] == "activeCompaign") {
              $acinteg = $website_integs->filter(function ($item) {
                return $item->name == "activeCompaign";
              })->first();
              if ($acinteg != null && $acinteg->key != null) {
                $integrations["ac"]['tags']         = $quiz->integration_ac_get_tags();
                $integrations["ac"]['answers_tags'] = $quiz->integration_ac_get_tagable_answers();
                $integrations["ac"]['ac_info']      = $quiz->integration_ac_get_lists();
                $integrations["ac"]['active_list']  = $quiz->integrations("activeCompaign");
                $integrations['ac']['field_values'] = collect($quiz->integration_ac_get_field_options())->keyBy('ac_value_id');
                if ($integrations["ac"]['active_list'] != null && $integrations["ac"]['active_list']->pivot != null && $integrations["ac"]['active_list']->pivot->value != null) {
                  $integrations["ac"]['is_used'] = true;
                  $integrations["ac"]['fields'] = $quiz->integration_ac_get_list_fields($integrations["ac"]['active_list']->pivot->value);
                  $values = $quiz->integration_ac; // ac integrations (fields values DB)
                  $types = ["Quiz", "Question", "Result", "Field", "Date"];
                  $fields_types = ["First-Name" => 3, "Email" => 3, "Last-Name" => 3, "Phone" => 3];
                  if ($integrations["ac"]['fields'] != null && count($integrations["ac"]['fields']) > 0 && $values->count() > 0) {
                    foreach ($integrations["ac"]["fields"] as $field) {
                      $done = false;
                      foreach ($values as $value) {
                        if (!$done && $value->type == $field->id) {
                          if (isset($integrations['ac']['field_values']->get($value->value)->ac_value_title)) {
                            $val = $integrations['ac']['field_values']->get($value->value);
                            $field->value = $val->ac_value_title;
                            $field->value_id = $val->ac_value_id;
                          }
                          $field->value_type = $value->value_type;
                          $done = true;
                        }
                      }
                    }
                  }
                }
              }
            } elseif ($integ["INTEGRATION"] == "convertKit") {
              $ckinteg = $website_integs->filter(function ($item) {
                return $item->name == "convertKit";
              })->first();
              if ($ckinteg != null && $ckinteg->key != null) {

                $integrations["ck"]['ck_info']      = $quiz->integration_ck_get_lists();

                if (!empty($integrations["ck"]['ck_info'])) {

                  $integrations["ck"]['tags']         = $quiz->integration_ck_get_tags();
                  $integrations["ck"]['active_list']  = $quiz->integrations("convertKit");
                  $integrations["ck"]['answers_tags'] = $quiz->integration_ck_get_tagable_answers();
                  $integrations['ck']['field_values'] = collect($quiz->integration_ck_get_field_options())->keyBy('ck_value_id');
                  $integrations["ck"]['fields'] = $quiz->integration_ck_get_list_fields();

                  if ($integrations["ck"]['active_list'] != null && $integrations["ck"]['active_list']->pivot != null && $integrations["ck"]['active_list']->pivot->value != null) {
                    $integrations["ck"]['is_used'] = true;

                    $values = $quiz->integration_ck; // ck integrations (fields values DB)
                    $types = ["Quiz", "Question", "Result", "Field", "Date"];
                    $fields_types = ["First-Name" => 3, "Email" => 3, "Last-Name" => 3, "Phone" => 3];
                    if ($integrations["ck"]['fields'] != null && count($integrations["ck"]['fields']) > 0 && $values->count() > 0) {
                      foreach ($integrations["ck"]["fields"] as $field) {
                        $done = false;
                        foreach ($values as $value) {
                          if (!$done && $value->type == $field->id) {
                            if (isset($integrations['ck']['field_values']->get($value->value)->ck_value_title)) {
                              $val = $integrations['ck']['field_values']->get($value->value);
                              $field->value = $val->ck_value_title;
                              $field->value_id = $val->ck_value_id;
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
            } elseif ($integ["INTEGRATION"] == "getResponse") {
              $grinteg = $website_integs->filter(function ($item) {
                return $item->name == "getResponse";
              })->first();
              if ($grinteg != null && $grinteg->key != null) {
                $integrations["gr"]['tags']         = $quiz->integration_gr_get_tags();
                $integrations["gr"]['answers_tags'] = $quiz->integration_gr_get_tagable_answers();
                $integrations["gr"]['gr_info']      = $quiz->integration_gr_get_lists();
                $integrations["gr"]['active_list']  = $quiz->integrations("getResponse");
                if (!empty($integrations["gr"]['active_list'])) {
                  $integrations["gr"]['is_used'] = true;
                }
                $integrations['gr']['field_values'] = collect($quiz->integration_gr_get_field_options())->keyBy('gr_value_id');
                $integrations["gr"]['fields'] = $quiz->integration_gr_get_list_fields();
                $values = $quiz->integration_gr;
                $types = ["Quiz", "Question", "Result", "Field", "Date"];
                $fields_types = ["First-Name" => 3, "Email" => 3, "Last-Name" => 3, "Phone" => 3];
                if ($values->count() > 0) {
                  foreach ($integrations["gr"]["fields"] as $field) {
                    $done = false;
                    foreach ($values as $value) {
                      if (!$done && $value->type == $field->customFieldId) {
                        if (isset($integrations['gr']['field_values']->get($value->value)->gr_value_title)) {
                          $val = $integrations['gr']['field_values']->get($value->value);
                          $field->value = $val->gr_value_title;
                          $field->value_id = $val->gr_value_id;
                        }
                        $field->value_type = $value->value_type;
                        $done = true;
                      }
                    }
                  }
                }
              }
              // dd($integrations["gr"]);
            } elseif ($integ["INTEGRATION"] == "googleAds") {
              $gainteg = $website_integs->filter(function ($item) {
                return $item->name == "googleAds";
              })->first();
              if ($gainteg != null && $gainteg->key != null && !empty($gainteg->key)) {
                $tags = $quiz->google_tags;
                $integrations["ga"]['start_remarket'] = $tags != null && $tags->start_marketing != null ? $tags->start_marketing : "";
                $integrations["ga"]['finish_remarket'] = $tags != null && $tags->finish_marketing != null ? $tags->finish_marketing : "";
                $integrations["ga"]['finish_conversion'] = $tags != null && $tags->finish_conversion != null ? $tags->finish_conversion : "";
                $integrations["ga"]['content_items'] = $quiz->integration_ga_content_items();
                if ($tags != null && !empty($tags)) {
                  if (!empty($integrations["ga"]['finish_conversion']) || !empty($integrations["ga"]['start_remarket']) || !empty($integrations["ga"]['finish_remarket']) || !empty($quiz->itemgoogles())) {
                    $integrations["ga"]['is_used'] = true;
                  }
                }
              }
            } elseif ($integ["INTEGRATION"] == "tagManager") {
              $tminteg = $website_integs->filter(function ($item) {
                return $item->name == "tagManager";
              })->first();
              if ($tminteg != null && $tminteg->key != null) {
                $integrations['tm'] = ["is_used" => null];
                $quiz_tm = $quiz->integrations("tagManager");
                if ($quiz_tm != null && $quiz_tm->pivot != null && $quiz_tm->pivot->value != null) {
                  $integrations['tm']['is_used'] = $quiz_tm->pivot->value == 1;
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
  }

  public function update_config(Quiz $quiz, Request $request) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {

      $validation_rules = [
        'meta_title'        => 'nullable',
        'meta_description'  => 'nullable',
        'policy_label'      => 'nullable',
        'policy_link'       => 'nullable|url',
      ];

      $validated = $request->validate($validation_rules);

      $activate_utm = request("activate_utm") ?? "off";
      $quiz->activate_utm = $activate_utm == "on" ? 1 : 0;

      $can_go_back = request("can_go_back") ?? "off";
      $quiz->can_go_back = $can_go_back == "on" ? 1 : 0;

      $show_logo = request("show_logo") ?? "off";
      $quiz->show_logo = $show_logo == "on" ? 1 : 0;

      $quiz->meta_title       = request("meta_title");
      $quiz->meta_description = request("meta_description");
      $quiz->policy_label     = request("policy_label");
      $quiz->policy_link      = request("policy_link");

      $res = $quiz->save();

      if ($res) {
        \Session::flash('saved', true);
      }

      return redirect()->route("config_quiz", compact("quiz"));
    } else {
      return abort(404);
    }
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
        File::copy(public_path("images/uploads/$template_info->image"), public_path("images/uploads/$new_image_name"));
        $copy->image = $new_image_name;
      }

      if ($template_info->intro_image != NULL && File::exists(public_path("images/uploads/$template_info->intro_image"))) {
        $unique_str = date('mdYHis') . uniqid();
        $new_image_name = $unique_str . substr($template_info->intro_image, strlen($unique_str));
        File::copy(public_path("images/uploads/$template_info->intro_image"), public_path("images/uploads/$new_image_name"));
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
              File::copy(public_path("images/uploads/$copyQuestion->image"), public_path("images/uploads/$new_image_name"));
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
                    File::copy(public_path("images/uploads/$duplicatedAnswer->image"), public_path("images/uploads/$new_image_name"));
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
    } else {
      return abort(404);
    }
  }

  public function update_status(Quiz $quiz, Request $request) {

    if (auth()->user()->is_admin() || $quiz->website->user->id == auth()->user()->id) {

      $validated = $request->validate([
        'publish' => 'required|integer|in:1,0',
      ]);

      $quiz->status = request("publish");

      return $quiz->save();
    } else {
      return false;
    }
  }

  public function report($quiz_id) {

    $questions = Question::where("quiz_id", $quiz_id)
      ->select(["questions.*", "en.title AS en_title", "ar.title AS ar_title"])
      ->join("question_translations AS en", "en.question_id", "=", DB::raw("questions.id AND en.locale = 'en'"))
      ->leftJoin("question_translations AS ar", "ar.question_id", "=", DB::raw("questions.id AND ar.locale = 'ar'"))
      ->withCount("entries")
      ->with("answers")
      ->get();

    $results = Result::where("quiz_id", $quiz_id)->get();

    $quiz = Quiz::where("id", $quiz_id)
      ->with("translations")
      ->withCount("nonempty_submissions")
      ->get()->first();

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {
      $responses = $quiz->nonempty_submissions_count;
      $completed = $quiz->submissions_is_done->count();

      return view("quizzes.report")->with(compact(
        "quiz",
        "questions",
        'results',
        'responses',
        'completed',
      ));
    } else {
      return abort(404);
    }
  }

  public function export_submissions(Quiz $quiz, Request $request) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {
      if (isset($request["exports"]) && !empty($request["exports"])) {

        $ids = explode(",", $request["exports"]);
        $submissions = Submission::whereIn("id", $ids)->get();

        $leads = [];
        // $submissions = $quiz->nonempty_submissions()->whereIn("id", )->get();
        $lang_key = app()->getLocale() . "_question_title";
        foreach ($submissions as $submission) {
          // $a = $submission->entries;
          // $b = $submission->entries_with_questions_answers;

          $info = [];
          $entries = $submission->report_entry();

          $info["id"] = $submission->id;
          $info["date"] = $submission->created_at->toDateTimeString();

          $q_count = 0;

          if ($entries->count() > 0) {

            foreach ($entries as $i => $entry) {

              if (!empty($entry->utm_name)) {
                $info[$entry->utm_name] = $entry->utm_value;
              }
              if (!empty($entry->$lang_key)) {
                if ($entry->fields->count() > 0) {
                  foreach ($entry->fields()->withPivot("value")->get() as $answer) {
                    $info[$i . "_" . $entry->$lang_key . "_" . str_replace(" ", "_", $answer->label)] = $answer->pivot->value;
                  }
                }
                if ($entry->answers->count() > 0) {
                  $info[$i . "_" . $entry->$lang_key] = '';
                  foreach ($entry->answers as $answer) {
                    $info[$i . "_" . $entry->$lang_key] .= $answer->text;
                  }
                }
              } else if (isset($entry->en_question_title) && !empty($entry->en_question_title)) {
                if ($entry->fields->count() > 0) {
                  foreach ($entry->fields()->withPivot("value")->get() as $answer) {
                    $info[$i . "_" . $entry->en_question_title . "_" . str_replace(" ", "_", $answer->label)] = $answer->pivot->value;
                  }
                }
                if ($entry->answers->count() > 0) {
                  $info[$i . "_" . $entry->en_question_title] = '';
                  foreach ($entry->answers as $answer) {
                    $info[$i . "_" . $entry->en_question_title] .= $answer->text;
                  }
                }
              }
            }
          }

          if ($submission->result_id != null) {
            $info[__("result")] = $submission->result->title;
          }

          array_push($leads, $info);
        }

        if (count($leads) > 0) {

          $fileName = time() . '_leads_export.csv';
          $headers = array(
            "Content-type"        => "text/csv;charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
          );

          $columns = array_keys($leads[0]);

          $callback = function () use ($leads, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($leads as $lead) {
              fputcsv($file, array_values($lead));
            }
            fclose($file);
          };

          return response()->stream($callback, 200, $headers);
        }

        /* ======== JSON EXPORT ======== */
        // if (count($leads) > 0) {

        //   $json_data = json_decode($leads);
        //   $fileName = time() . '_leads_export.json';
        //   $fileStorePath = public_path('exports/' . $fileName);
        //   $file = File::put($fileStorePath, $json_data);

        //   $headers = array(
        //     'Content-Type: application/json',
        //   );
        //   if ($file) {
        //     File::delete($fileStorePath);
        //     return response()->download(
        //       $fileStorePath,
        //       $fileName,
        //       $headers
        //     );
        //   }
        // }

      }

      return redirect()->route("report_quiz", $quiz->id);
    }
  }

  public function get_submission($submission_id, Request $request) {

    $submission = Submission::where("id", $submission_id)->with("result")->get()->first();

    if ($submission != null) {
      $entries = $submission->report_entry();

      if ($entries->count() > 0) {
        foreach ($entries as $entry) {
          if (count($entry->answers_report) > 0) {
            $entry->answers_report;
          }
          if (count($entry->fields_label_value) > 0) {
            $fields = $entry->fields_label_value;
            foreach ($fields as $field) {
              if (in_array($field->type, [8])) {
                $field->value = Option::find($field->value)->value;
              }
            }
          }
        }
      }

      $submission->entries = $entries;
      return $submission;
    } else {
      return abort(404);
    }
  }

  public function update(Quiz $quiz, Request $request) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {

      $rules = [
        'en_intro_title' => 'required|string|max:255',
        'en_intro_btn' => 'required|string|max:120',
        'quiz-intro-img' => 'sometimes|nullable|image|mimes:png,jpg,jpeg|max:2048', // max = 2 mega byte
        'quiz-intro-url' => 'sometimes|nullable|url',
        'show_policy' => 'sometimes|required',
        'en_template_desc' => 'sometimes|nullable|max:255',
      ];

      // if (auth()->user()->can_translate()) {
      //   $rules['ar_intro_title'] = 'required|string|max:255';
      //   $rules['ar_intro_btn'] = 'required|string|max:120';
      // }

      $request->validate($rules);

      if (auth()->user()->can_translate()) {

        $request->validate([
          'ar_intro_title' => 'sometimes|max:255',
          'ar_intro_btn' => 'sometimes|max:120',
          'ar_template_desc' => 'sometimes|nullable|max:255',
        ]);

        foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties) {
          $quiz->translateOrNew($localeCode)->intro_title = request($localeCode . '_intro_title');
          $quiz->translateOrNew($localeCode)->intro_description = request($localeCode . '_intro_description');
          $quiz->translateOrNew($localeCode)->intro_btn = request($localeCode . '_intro_btn');
          if ($quiz->is_template == 1) {
            $quiz->translateOrNew($localeCode)->template_desc = request($localeCode . '_template_desc') ?? "";
          }
        }
      } else {

        $quiz->intro_title        = request('en_intro_title');
        $quiz->intro_description  = request('en_intro_description');
        $quiz->intro_btn          = request('en_intro_btn');
        if ($quiz->is_template == 1) {
          $quiz->template_desc = request('en_template_desc');
        }
      }

      if (request('quiz-intro-img') != null && request('quiz-intro-url') == null) {
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
      if (request('quiz-intro-url') == null && request('quiz-intro-img') == null) {
        $quiz->intro_image = null;
      }

      if ($quiz->policy_link != null) {
        $quiz->is_shown_policy = request('show_policy') == "on" ? 1 : 0;
      }

      $quiz->save();
      return redirect()->route("build_quiz", compact("quiz"));
    } else {
      return abort(404);
    }
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
    } else {
      return false;
    }
  }

  public function popup_images(Quiz $quiz, Request $request) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {

      $request->validate([
        "image" => "nullable|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
        "thumb" => "nullable|image|mimes:png,jpg,jpeg|max:2048",
      ]);

      if ($request->hasFile('image') && $quiz->popup_img != null && File::exists(public_path("images/uploads/$quiz->popup_img"))) {
        File::delete(public_path("images/uploads/$quiz->popup_img"));
      }
      if ($request->hasFile('thumb') && $quiz->popup_thumb != null && File::exists(public_path("images/uploads/$quiz->popup_thumb"))) {
        File::delete(public_path("images/uploads/$quiz->popup_thumb"));
      }

      if ($request->hasFile('image')) {
        $quiz->popup_img = date('mdYHis') . uniqid() . $request->file('image')->getClientOriginalName();
        $request->image->move(public_path('images/uploads'), $quiz->popup_img);
      }

      if ($request->hasFile('thumb')) {
        $quiz->popup_thumb = date('mdYHis') . uniqid() . $request->file('thumb')->getClientOriginalName();
        $request->thumb->move(public_path('images/uploads'), $quiz->popup_thumb);
      }

      $quiz->save();

      return redirect()->route("sharing_quiz", ["quiz" => $quiz->id, "page" => "popup-page"]);
    } else {
      return false;
    }
  }
}
