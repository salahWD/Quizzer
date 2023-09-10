<?php

namespace App\Http\Controllers;

use File;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Result;
use App\Models\Condition;
use App\Models\Field;
use App\Models\QuestionTranslation;
use App\Models\AnswerTranslation;
use Illuminate\Validation\ValidationException;// throwing custom validation errors

// throw ValidationException::withMessages(['field_name' => 'This value is incorrect']);

class QuestionController extends Controller {

  public function __construct() {
    $this->middleware('auth')->except("index");
  }

  public function store(Quiz $quiz, Request $request) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user->id) {

      $request->validate(["type_id" => "required|integer|min:1|max:5"]);

      if ($quiz->type == 1) {// type 1 => scoring

        if (in_array(request("type_id"), [1, 2])) {
          $validate_ruls = [
            "image"               => "nullable|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte
            "video"               => "nullable|url",
            "en_question_title"   => "required|max:200",
            "en_question_desc"    => "max:400",
            "ar_question_title"   => "sometimes|required|string|max:200",
            "ar_question_desc"    => "sometimes|nullable|max:400",
            "en_answers"          => "required|array|min:2",
            "en_answers.*.image"  => "sometimes|nullable|image|mimes:png,jpg,jpeg|max:2048",
            "en_answers.*.text"   => "required",
            "en_answers.*.ar_text" => "sometimes|nullable",
            "en_answers.*.score"  => "nullable|integer",
            "en_answers.*.order"  => "required|integer|min:1",
            "order"               => "required|integer|min:1",
            "multi_select"        => "required|boolean",
          ];

          if (request("type_id") == 2) {

            $validate_ruls["en_answers.*.image"] = 'required|image|mimes:png,jpg,jpeg|max:2048';
          }

        }elseif (request("type_id") == 3) {
          $validate_ruls = [
            "media_type"      => "required|in:image,video",
            "video"           => "nullable|exclude_if:media_type,image|url",
            "image"           => "nullable|exclude_if:media_type,video|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte",
            "en_question_title"  => "required|max:200",
            "en_question_desc"   => "nullable|max:400",
            "en_button_label"    => "required|string|max:20",
            "ar_question_title"  => "sometimes|required|string|max:200",
            "ar_question_desc"   => "sometimes|nullable|max:400",
            "ar_button_label"    => "sometimes|string|max:20",
            "is_skippable"    => "required|in:true,false",
            "show_policy"     => "required|in:true,false",
            "order"           => "required|integer|min:1",
            "fields"            => "required|array|max:16",
            "fields.*.label"    => "required|string|max:120",
            "fields.*.order"    => "required|string|max:120",
            "fields.*.placeholder" => "nullable|string|max:120",
            "fields.*.type"     => "required|integer|min:1|max:13",
          ];
        }elseif (request("type_id") == 4) {
          $validate_ruls = [
            "en_question_title"  => "required|max:200",
            "en_question_desc"   => "max:400",
            "en_button_label"    => "required|max:20",
            "ar_question_title"  => "sometimes|required|string|max:200",
            "ar_question_desc"   => "sometimes|nullable|max:400",
            "ar_button_label"    => "sometimes|nullable|max:20",
            "order"           => "required|integer|min:1",
          ];
        }elseif (request("type_id") == 5) {
          $validate_ruls = [
            "media_type"      => "required|in:image,video",
            "video"           => "required_if:media_type,video|exclude_if:media_type,image|url",
            "image"           => "required_if:media_type,image|exclude_if:media_type,video|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte",
            "en_question_title"  => "required|max:200",
            "en_question_desc"   => "max:400",
            "en_button_label"    => "required|max:20",
            "ar_question_title"  => "sometimes|required|string|max:200",
            "ar_question_desc"   => "sometimes|nullable|max:400",
            "ar_button_label"    => "sometimes|nullable|max:20",
            "order"           => "required|integer|min:1",
          ];
        }

        $request->validate($validate_ruls);

        if (in_array(request("type_id"), [1, 2])) {

          if ($request->hasFile('image')) {
            $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . substr($request->file('image')->getClientOriginalName(), -10));
            $request->image->move(public_path('images/uploads'), $unique_name);
          }

          $questionInfo = [
            "quiz_id" => $quiz->id,
            "type" => request("type_id"),
            "image" => $unique_name ?? null,
            "video" => request("video"),
            "en" => [
              "title" => request("en_question_title"),
              "description" => request("en_question_desc"),
            ],
            "multi_select" => $request->boolean("multi_select"),
            "order" => request("order"),
          ];

          if (!empty(request("ar_question_title")) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
            $questionInfo["ar"] = [
              "title" => request("ar_question_title"),
              "description" => request("ar_question_desc"),
            ];
          }
          $question = Question::create($questionInfo);

          // Answers Handlers
          if (request("type_id") == 1) {// text question

            foreach (request("en_answers") as $answer) {
              $answerInfo = [
                "question_id" => $question->id,
                'en' => ['text' => $answer["text"]],
                "score" => $answer["score"],
                "order" => $answer["order"]
              ];
              if (!empty($answer["ar_text"]) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
                $answerInfo['ar'] = ['text' => $answer["ar_text"]];
              }
              Answer::create($answerInfo);
            }

          }elseif (request("type_id") == 2) {// image question

            foreach (request("en_answers") as $i => $answer) {

              $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . substr($answer["image"]->getClientOriginalName(), -10));
              $answer["image"]->move(public_path('images/uploads'), $unique_name);
              $answerInfo = [
                "question_id" => $question->id,
                'en' => ['text' => $answer["text"]],
                "score" => $answer["score"],
                "order" => $answer["order"],
                "image" => $unique_name
              ];
              if (!empty($answer["ar_text"])) {
                $answerInfo['ar'] = ['text' => $answer["ar_text"]];
              }
              Answer::create($answerInfo);
            }

          }

        }elseif (request("type_id") == 3) {

          $questino_info = [
            "quiz_id" => $quiz->id,
            "type" => request("type_id"),
            "en" => [
              "title" => $request["en_question_title"],
              "description" => $request["en_question_desc"],
              "button_label" => $request["en_button_label"],
            ],
            "is_required" => request("is_required") == "true" ? 1 : 0,
            "is_skippable" => $request["is_skippable"] == "true" ? 1 : 0,
            "show_policy" => $request["show_policy"] == "true" ? 1 : 0,
            "order" => $request["order"],
          ];

          if (!empty(request("ar_question_title")) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
            $questino_info["ar"] = [
              "title" => null !== request("ar_question_title") && !empty(request("ar_question_title")) ? request("ar_question_title") : "",
              "description" => null !== request("ar_question_desc") && !empty(request("ar_question_desc")) ? request("ar_question_desc") : "",
              "button_label" => null !== request("ar_button_label") && !empty(request("ar_button_label")) ? request("ar_button_label") : "",
            ];
          }

          if (request("media_type") == "video" && isset($request["video"])) {
            $questino_info["video"] = $request["video"];
          }elseif (request("media_type") == "image" && isset($request["image"])) {
            $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . substr($request["image"]->getClientOriginalName(), -10));
            $request["image"]->move(public_path('images/uploads'), $unique_name);
            $questino_info["image"] = $unique_name;
          }

          $question = Question::create($questino_info);

          foreach (request("fields") as $field) {

            $field_obj = [
              "question_id" => $question->id,
              "order" => $field["order"],
              "type" => $field["type"],
              "en" => [
                "label" => $field["label"],
              ],
            ];

            if (in_array($field["type"], [1, 2, 3, 4, 5, 6, 8, 11, 12])) {
              $field_obj["en"]["placeholder"] = $field["placeholder"];
              if (!empty($field["ar_label"]) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
                $field_obj["ar"]["label"] = $field["ar_label"];
                $field_obj["ar"]["placeholder"] = $field["ar_placeholder"];
              }
            }
            if ($field["type"] == 3) {
              $field_obj["is_lead_email"] = $field["is_lead_email"];
            }
            if ($field["type"] != 13) {
              $field_obj["is_required"] = $field["is_required"];
            }
            if ($field["type"] == 13) {
              $field_obj["hidden_value"] = $field["hidden_value"];
            }

            $new_field = Field::create($field_obj);

            if (in_array($field["type"], [7, 8])) {
              $options = [];
              foreach($field["options"] as $option) {
                array_push($options, [
                  "field_id" => $new_field->id,
                  "value" => $option,
                ]);
              }
              $new_field->options()->createMany($options);
            }

          }

        }elseif (request("type_id") == 4) {

          $questino_info = [
            "quiz_id" => $quiz->id,
            "type" => request("type_id"),
            "en" => [
              "title" => request("en_question_title"),
              "description" => request("en_question_desc"),
              "button_label" => request("en_button_label"),
            ],
            "order" => request("order"),
          ];
          if (!empty(request("ar_question_title")) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
            $questino_info["ar"] = [
              "title" => request("ar_question_title"),
              "description" => request("ar_question_desc"),
              "button_label" => request("ar_button_label"),
            ];
          }
          $question = Question::create($questino_info);

        }elseif (request("type_id") == 5) {

          $questino_info = [
            "quiz_id" => $quiz->id,
            "type" => request("type_id"),
            "en" => [
              "title" => request("en_question_title"),
              "description" => request("en_question_desc"),
              "button_label" => request("en_button_label"),
            ],
            "order" => request("order"),
          ];
          if (!empty(request("ar_question_title")) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
            $questino_info["ar"] = [
              "title" => request("ar_question_title"),
              "description" => request("ar_question_desc"),
              "button_label" => request("ar_button_label"),
            ];
          }
          if (request("media_type") == "video") {
            $questino_info["video"] = request("video");
          }elseif (request("media_type") == "image") {
            $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . substr(request("image")->getClientOriginalName(), -10));
            request("image")->move(public_path('images/uploads'), $unique_name);
            $questino_info["image"] = $unique_name;
          }

          $question = Question::create($questino_info);
        }

      }elseif ($quiz->type == 2) {// type 2 => outcome

        if (in_array(request("type_id"), [1, 2])) {
          $validate_ruls = [
            "image"           => "nullable|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte
            "video"           => "nullable|url",
            "en_question_title"  => "required|max:200",
            "en_question_desc"   => "max:400",
            "ar_question_title"  => "sometimes|required|string|max:200",
            "ar_question_desc"   => "sometimes|nullable|max:400",
            "en_answers"         => "required|array|min:2",
            "en_answers.*.image"   => "sometimes|nullable|image|mimes:png,jpg,jpeg|max:2048",
            "en_answers.*.text"  => "required",
            "en_answers.*.ar_text" => "sometimes|nullable",
            "en_answers.*.order" => "required|integer|min:1",
            "order"           => "required|integer|min:1",
            "multi_select"    => "required|boolean",
          ];
        }elseif (request("type_id") == 3) {
          $validate_ruls = [
            "media_type"      => "required|in:image,video",
            "video"           => "nullable|exclude_if:media_type,image|url",
            "image"           => "nullable|exclude_if:media_type,video|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte",
            "en_question_title"  => "nullable|max:200",
            "en_question_desc"   => "max:400",
            "en_button_label"    => "nullable|string|max:20",
            "ar_question_title"  => "sometimes|required|string|max:200",
            "ar_question_desc"   => "sometimes|nullable|max:400",
            "ar_button_label"    => "sometimes|string|max:20",
            "is_skippable"    => "required|string|in:true,false",
            "show_policy"     => "required|string|in:true,false",
            "order"           => "required|integer|min:1",
            "fields"            => "required|array|max:16",
            "fields.*.label"    => "required|string|max:120",
            "fields.*.order"    => "required|string|max:120",
            "fields.*.placeholder" => "nullable|string|max:120",
            "fields.*.type"     => "required|integer|min:1|max:13",
          ];
        }elseif (request("type_id") == 4) {
          $validate_ruls = [
            "en_question_title"  => "required|max:200",
            "en_question_desc"   => "max:400",
            "en_button_label"    => "required|max:20",
            "ar_question_title"  => "sometimes|required|string|max:200",
            "ar_question_desc"   => "sometimes|nullable|max:400",
            "ar_button_label"    => "sometimes|nullable|max:20",
            "order"           => "required|integer|min:1",
          ];
        }elseif (request("type_id") == 5) {
          $validate_ruls = [
            "media_type"      => "required|in:image,video",
            "video"           => "required_if:media_type,video|exclude_if:media_type,image|url",
            "image"           => "required_if:media_type,image|exclude_if:media_type,video|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte",
            "en_question_title"  => "required|max:200",
            "en_question_desc"   => "max:400",
            "en_button_label"    => "required|max:20",
            "ar_question_title"  => "sometimes|required|string|max:200",
            "ar_question_desc"   => "sometimes|nullable|max:400",
            "ar_button_label"    => "sometimes|nullable|max:20",
            "order"           => "required|integer|min:1",
          ];
        }

        $request->validate($validate_ruls);

        if (in_array(request("type_id"), [1, 2])) {

          if ($request->hasFile('image')) {
            $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . substr($request->file('image')->getClientOriginalName(), -10));
            $request->image->move(public_path('images/uploads'), $unique_name);
          }

          $question_info = [
            "quiz_id" => $quiz->id,
            "type" => request("type_id"),
            "image" => $unique_name ?? null,
            "video" => request("video"),
            'en' => [
              "title" => request("en_question_title"),
              "description" => request("en_question_desc"),
            ],
            "multi_select" => $request->boolean("multi_select"),
            "order" => request("order"),
          ];

          if (!empty(request("ar_question_title")) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
            $question_info['ar'] = [
              "title" => request("ar_question_title"),
              "description" => request("ar_question_desc"),
            ];
          }

          $question = Question::create($question_info);

          // Answers Handlers
          if (request("type_id") == 1) {// text question

            foreach (request("en_answers") as $answer) {

              $answer_info = [
                "question_id" => $question->id,
                "en" => ["text" => $answer["text"]],
                "order" => $answer["order"]
              ];

              if (!empty($answer["ar_text"]) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
                $answer_info['ar'] = ["text" => $answer["ar_text"] ?? ""];
              }
              Answer::create($answer_info);
            }

          }elseif (request("type_id") == 2) {// image question

            $request->validate([
              "en_answers.*.image" => "required|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte
            ]);

            foreach (request("en_answers") as $i => $answer) {

              $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . substr($answer["image"]->getClientOriginalName(), -10));
              $answer["image"]->move(public_path('images/uploads'), $unique_name);

              $answer_info = [
                "question_id" => $question->id,
                "en" => ["text" => $answer["text"],],
                "order" => $answer["order"],
                "image" => $unique_name
              ];

              if (!empty($answer["ar_text"]) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
                $answer_info['ar'] = ["text" => $answer["ar_text"] ?? ""];
              }
              Answer::create($answer_info);
            }

          }

        }elseif (request("type_id") == 3) {

          $questino_info = [
            "quiz_id" => $quiz->id,
            "type" => request("type_id"),
            'en' => [
              "title" => request("en_question_title"),
              "description" => request("en_question_desc"),
              "button_label" => request("en_button_label"),
            ],
            "is_skippable" => request("is_skippable") == "true" ? 1 : 0,
            "show_policy" => request("show_policy") == "true" ? 1 : 0,
            "order" => request("order"),
          ];

          if (!empty(request("ar_question_title")) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
            $questino_info['ar'] = [
              "title" => request("ar_question_title"),
              "description" => request("ar_question_desc"),
              "button_label" => request("ar_button_label"),
            ];
          }

          if (request("media_type") == "video" && isset($request["video"])) {
            $questino_info["video"] = $request["video"];
          }elseif (request("media_type") == "image" && isset($request["image"])) {
            $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . substr($request["image"]->getClientOriginalName(), -10));
            $request["image"]->move(public_path('images/uploads'), $unique_name);
            $questino_info["image"] = $unique_name;
          }

          $question = Question::create($questino_info);

          foreach (request("fields") as $field) {

            $field_obj = [
              "question_id" => $question->id,
              "order" => $field["order"],
              "type" => $field["type"],
              "en" => [
                "label" => $field["label"],
              ],
            ];

            if (in_array($field["type"], [1, 2, 3, 4, 5, 6, 8, 11, 12])) {
              $field_obj["en"]["placeholder"] = $field["placeholder"];
              if (!empty($field["ar_label"]) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
                $field_obj["ar"]["label"] = $field["ar_label"];
                $field_obj["ar"]["placeholder"] = $field["ar_placeholder"];
              }
            }
            if ($field["type"] == 3) {
              $field_obj["is_lead_email"] = $field["is_lead_email"];
            }
            if ($field["type"] != 13) {
              $field_obj["is_required"] = $field["is_required"];
            }
            if ($field["type"] == 13) {
              $field_obj["hidden_value"] = $field["hidden_value"];
            }

            $new_field = Field::create($field_obj);

            if (in_array($field["type"], [7, 8])) {
              $options = [];
              foreach($field["options"] as $option) {
                array_push($options, [
                  "field_id" => $new_field->id,
                  "value" => $option,
                ]);
              }
              $new_field->options()->createMany($options);
            }

          }

        }elseif (request("type_id") == 4) {

          $question_info = [
            "quiz_id" => $quiz->id,
            "type" => request("type_id"),
            'en' => [
              "title" => request("en_question_title"),
              "description" => request("en_question_desc"),
              "button_label" => request("en_button_label"),
            ],
            "order" => request("order"),
          ];

          if (!empty(request("ar_question_title")) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
            $question_info['ar'] = [
              "title" => request("ar_question_title"),
              "description" => request("ar_question_desc"),
              "button_label" => request("ar_button_label"),
            ];
          }

          $question = Question::create($question_info);

        }elseif (request("type_id") == 5) {

          $questino_info = [
            "quiz_id" => $quiz->id,
            "type" => request("type_id"),
            'en' => [
              "title" => request("en_question_title"),
              "description" => request("en_question_desc"),
              "button_label" => request("en_button_label"),
            ],
            "order" => $request["order"],
          ];

          if (!empty(request("ar_question_title")) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
            $questino_info['ar'] = [
              "title" => request("ar_question_title"),
              "description" => request("ar_question_desc"),
              "button_label" => request("ar_button_label"),
            ];
          }

          if (request("media_type") == "video") {
            $questino_info["video"] = $request["video"];
          }elseif (request("media_type") == "image") {
            $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . substr($request["image"]->getClientOriginalName(), -10));
            $request["image"]->move(public_path('images/uploads'), $unique_name);
            $questino_info["image"] = $unique_name;
          }

          $question = Question::create($questino_info);
        }

      }

      return $question->id;

    }else {
      return false;
    }

  }

  public function copy(Question $question, Request $request) {

    if (auth()->user()->is_admin() || auth()->user()->id == $question->quiz->website->user->id) {

      $order = $question->largest_order()[0]["largest_order"];
      $newQuestion = $question->replicateWithTranslations();
      $newQuestion->order = $order + 1;

      if (in_array($question->type, [1, 2, 3, 5])) {
        if ($question->image != NULL && File::exists(public_path("images/uploads/$question->image"))) {
          $unique_str = date('mdYHis') . uniqid();
          $new_image_name = $unique_str . substr($question->image, strlen($unique_str));
          File::copy(public_path("images/uploads/$question->image") , public_path("images/uploads/$new_image_name"));
          $newQuestion->image = $new_image_name;
        }
      }

      $newQuestion->save();

      if (in_array($question->type, [1, 2])) {
        $answres = [];
        if ($question->type == 1) {
          foreach ($question->answers as $answer) {
            $duplicatedAnswer = $answer->replicateWithTranslations();
            $duplicatedAnswer->question_id = $newQuestion->id;
            $answres[] = $duplicatedAnswer;
          }
        }else if ($question->type == 2) {
          foreach ($question->answers as $answer) {
            $duplicatedAnswer = $answer->replicateWithTranslations();
            $duplicatedAnswer->question_id = $newQuestion->id;
            if ($answer->image != NULL && File::exists(public_path("images/uploads/$answer->image"))) {
              $unique_str = date('mdYHis') . uniqid();
              $new_image_name = $unique_str . substr($answer->image, strlen($unique_str));
              File::copy(public_path("images/uploads/$answer->image") , public_path("images/uploads/$new_image_name"));
              $duplicatedAnswer->image = $new_image_name;
            }
            $answres[] = $duplicatedAnswer;
          }
        }
        $newQuestion->answers()->saveMany($answres);
      }

      return $newQuestion->id;
    }else {
      return false;
    }

  }

  public function show(Question $question) {

    if (auth()->user()->is_admin() || auth()->user()->id == $question->quiz->website->user->id) {

      // $question->langs_translate();
      $question->setDefaultLocale(app()->getLocale());

      if (in_array($question->type, [1, 2])) {
        $question->answers = $question->langs_answers;
      }elseif ($question->type == 3) {
        $question->fields = $question->fields()->withTranslation()->get();
      }

      return $question;
    }else {
      if ($question->quiz->status == 1) {// quiz is publish
        $question->translate(app()->getLocale());
        if (in_array($question->type, [1, 2])) {
          $LangAnswers = app()->getLocale() . "_answers";
          $question->answers = $question->$LangAnswers();
        }
        return $question;
      }
    }
    return false;
  }

  public function image_actions(Question $question, Request $request) {

    $request->validate([
      "action" => "required|in:remove",
    ]);

    if (auth()->user()->is_admin() || auth()->user()->id == $question->quiz->website->user->id) {

      if (request("action") == "remove") {
        if ($question->image != null && File::exists(public_path("images/uploads/$question->image"))) {
          File::delete(public_path("images/uploads/$question->image"));
        }
        $question->image = null;
      }

      return $question->save();
    }
    return false;
  }

  public function update(Question $question, Request $request) {

    if (auth()->user()->is_admin() || auth()->user()->id == $question->quiz->website->user->id) {

      $request->validate(["type_id" => "required|integer|min:1|max:5"]);

      if ($question->quiz->type == 1) {// type 1 => scoring

        if (in_array(request("type_id"), [1, 2])) {
          $validate_ruls = [
            "image"               => "nullable|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte
            "video"               => "nullable|url",
            "en_question_title"   => "required|max:200",
            "en_question_desc"    => "max:400",
            "ar_question_title"   => "sometimes|required|string|max:200",
            "ar_question_desc"    => "sometimes|nullable|max:400",
            "en_answers"          => "required|array|min:2",
            "en_answers.*.id"     => "nullable|integer|exists:answers",
            "en_answers.*.text"   => "required",
            "en_answers.*.ar_text" => "sometimes|nullable",
            "en_answers.*.score"  => "required|integer",
            "en_answers.*.order"  => "required|integer|min:1",
            "en_answers.*.action" => "sometimes|in:remove",
            "multi_select"        => "required|boolean",
          ];
        }else if (request("type_id") == 3) {
          $validate_ruls = [
            "media_type"      => "required|in:image,video",
            "video"           => "nullable|exclude_if:media_type,image|url",
            "image"           => "nullable|exclude_if:media_type,video|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte",
            "en_question_title" => "required|max:200",
            "en_question_desc"  => "nullable|max:400",
            "en_button_label"   => "required|string|max:20",
            "ar_question_title" => "sometimes|required|string|max:200",
            "ar_question_desc"  => "sometimes|nullable|max:400",
            "ar_button_label"   => "sometimes|string|max:20",
            "is_skippable"    => "required|in:true,false",
            "show_policy"     => "required|in:true,false",
            "fields"            => "required|array|max:16",
            "fields.*.id"       => "sometimes|required|integer|exists:fields,id",
            "fields.*.label"    => "required|string|max:120",
            "fields.*.order"    => "required|string|max:120",
            "fields.*.placeholder" => "nullable|string|max:120",
            "fields.*.type"     => "required|integer|min:1|max:13",
            "fields.*.is_multiple_chooseing" => "required_if:fields.*.type,7|integer|in:0,1",
            "fields.*.options" => "required_if:fields.*.type,7|array|min:1",
            "fields.*.options.*" => "required_if:fields.*.type,7|string|max:255",
          ];
        }else if (request("type_id") == 4) {
          $validate_ruls = [
            "en_question_title"  => "required|max:200",
            "en_question_desc"   => "max:400",
            "en_button_label"    => "required|max:20",
            "ar_question_title"  => "sometimes|required|string|max:200",
            "ar_question_desc"   => "sometimes|nullable|max:400",
            "ar_button_label"    => "sometimes|string|max:20",
          ];
        }else if (request("type_id") == 5) {
          $validate_ruls = [
            "media_type"      => "required|in:image,video",
            "video"           => "required_if:media_type,video|url",
            "image"           => "nullable|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte",
            "en_question_title"  => "required|max:200",
            "en_question_desc"   => "nullable|max:400",
            "en_button_label"    => "required|max:20",
            "ar_question_title"  => "sometimes|required|string|max:200",
            "ar_question_desc"   => "sometimes|nullable|max:400",
            "ar_button_label"    => "sometimes|string|max:20",
          ];
        }

        $request->validate($validate_ruls);

        if (in_array(request("type_id"), [1, 2])) {

          if ($request->hasFile('image')) {

            $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . $request->image->getClientOriginalName());
            $request->image->move(public_path('images/uploads'), $unique_name);
            $old_image = $question->image;
            $question->image = $unique_name;

            if (File::exists(public_path("images/uploads/") . $old_image)) {
              File::delete(public_path("images/uploads/") . $old_image);
            }
          }

          $question->video = request("video");
          $question->translateorNew('en')->title       = request("en_question_title");
          $question->translateorNew('en')->description = request("en_question_desc");
          if (!empty(request("ar_question_title")) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
            $question->translateOrNew('ar')->title       = request("ar_question_title");
            $question->translateOrNew('ar')->description = request("ar_question_desc");
          }
          $question->multi_select = $request->boolean("multi_select");

          // Answers Handlers
          $answers = $question->answers;

          if (request("type_id") == 1) {// text question

            foreach (request("en_answers") as $answer) {
              if (isset($answer["id"]) && !empty($answer["id"]) && is_numeric((int)$answer["id"])) {
                foreach ($answers as $DBAnswer) {
                  if ($DBAnswer->id == $answer["id"]) {
                    if (isset($answer["action"]) && $answer["action"] == "remove") {
                      $DBAnswer->delete();
                    }else {
                      $DBAnswer->text = $answer["text"];
                      if (!empty($answer["ar_text"]) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
                        $DBAnswer->translateOrNew("ar")->text = $answer["ar_text"] ?? "";
                      }
                      $DBAnswer->score = $answer["score"];
                      $DBAnswer->order = $answer["order"];
                      $DBAnswer->save();
                    }
                  }
                }
              }else {
                $createInfo = [
                  "question_id" => $question->id,
                  'en' => ['text' => $answer["text"]],
                  "score" => $answer["score"],
                  "order" => $answer["order"]
                ];
                if (!empty($answer["ar_text"]) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
                  $createInfo["ar"] = ['text' => $answer["ar_text"] ?? ""];
                }
                Answer::create($createInfo);
              }
            }

          }elseif (request("type_id") == 2) {// image question

            foreach (request("en_answers") as $i => $answer) {

              if (isset($answer["id"]) && !empty($answer["id"]) && is_numeric((int)$answer["id"])) {
                foreach ($answers as $DBAnswer) {
                  if ($DBAnswer->id == $answer["id"]) {
                    if (isset($answer["action"]) && $answer["action"] == "remove") {
                      $DBAnswer->delete();
                      if ($DBAnswer->image != null && File::exists(public_path("images/uploads/") . $DBAnswer->image)) {
                        File::delete(public_path("images/uploads/") . $DBAnswer->image);
                      }
                    }else {
                      $DBAnswer->text = $answer["text"];
                      $DBAnswer->translateOrNew("ar")->text = $answer["ar_text"] ?? "";
                      $DBAnswer->score = $answer["score"];
                      $DBAnswer->order = $answer["order"];
                      if ($request->hasFile("en_answers.$i")) {
                        $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . $answer["image"]->getClientOriginalName());
                        $answer["image"]->move(public_path('images/uploads'), $unique_name);
                        $old_image = $DBAnswer->image;
                        if ($old_image != null && File::exists(public_path("images/uploads/") . $old_image)) {
                          File::delete(public_path("images/uploads/") . $old_image);
                        }
                        $DBAnswer->image = $unique_name;
                      }
                      $DBAnswer->save();
                    }
                  }
                }
              }else {

                $request->validate([
                  "en_answers.$i.image" => "required|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte
                ]);

                $unique_name = "default.jpg";
                if ($request->hasFile("en_answers.$i")) {
                  $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . $answer["image"]->getClientOriginalName());
                  $answer["image"]->move(public_path('images/uploads'), $unique_name);
                }

                Answer::create([
                  "question_id" => $question->id,
                  "en" => ["text" => $answer["text"]],
                  "ar" => ["text" => $answer["ar_text"] ?? ""],
                  "score" => $answer["score"],
                  "order" => $answer["order"],
                  "image" => $unique_name
                ]);
              }
            }

          }

        }else if (request("type_id") == 3) {

          if (request("media_type") == "video" && isset($request["video"])) {
            $question->video = $request["video"];
            $question->image = NULL;
          }elseif (request("media_type") == "image" && isset($request["image"])) {
            if ($question->image != NULL && File::exists(public_path("images/uploads/$question->image"))) {
              File::delete(public_path("images/uploads/$question->image"));
            }
            $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . $request["image"]->getClientOriginalName());
            $request["image"]->move(public_path('images/uploads'), $unique_name);
            $question->image = $unique_name;
            $question->video = NULL;
          }

          $question->translateOrNew("en")->title        = $request["en_question_title"];
          $question->translateOrNew("en")->description  = $request["en_question_desc"];
          $question->translateOrNew("en")->button_label = $request["en_button_label"];
          if (!empty(request("ar_question_title")) && !empty(request("ar_button_label")) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
            $question->translateOrNew('ar')->title       = request("ar_question_title");
            $question->translateOrNew('ar')->description = request("ar_question_desc");
            $question->translateOrNew("ar")->button_label = request("ar_button_label");
          }
          $question->is_skippable = $request["is_skippable"] == "true" ? 1 : 0;
          $question->show_policy  = $request["show_policy"] == "true" ? 1 : 0;

          $fields_ids = [];
          $fields = $question->fields;
          $DbFieldsIds = $fields->pluck("id")->toArray();

          foreach (request("fields") as $field) {
            if (isset($field["id"]) && null !== $field["id"]) {
              if (in_array($field["id"], $DbFieldsIds)) {

                $value = $fields->filter(function ($obj) use ($field) {
                  return $obj->id == $field["id"];
                })->first();

                if ($value !== null && !empty($value)) {

                  $en = $value->translateOrNew("en");
                  $en->label = $field["label"];
                  $value->order = $field["order"];

                  if (in_array($value->type, [1, 2, 3, 4, 5, 6, 8, 11, 12])) {// placeholder
                    $en->placeholder = $field["placeholder"];
                  }

                  if (!empty($field["ar_label"]) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
                    $ar = $value->translateOrNew("ar");
                    $ar->label = $field["ar_label"];
                    if (in_array($value->type, [1, 2, 3, 4, 5, 6, 8, 11, 12])) {
                      $ar->placeholder = $field["ar_placeholder"];
                    }
                  }

                  if ($value->type != 13) {
                    $value->is_required = $field["is_required"];
                  }
                  if ($field["type"] == 3) {
                    $value->is_lead_email = $field["is_lead_email"];
                  }
                  if ($field["type"] == 7) {
                    $value->is_multiple_chooseing = $field["is_multiple_chooseing"];
                  }
                  if ($field["type"] == 13) {
                    $value->hidden_value = $field["hidden_value"];
                  }

                  $value->save();
                  array_push($fields_ids, $value->id);

                  if (in_array($field["type"], [7, 8])) {
                    $options = [];
                    foreach($field["options"] as $option) {
                      array_push($options, [
                        "field_id" => $value->id,
                        "value" => $option,
                      ]);
                    }
                    $value->options()->delete();
                    $value->options()->createMany($options);
                  }

                }

              }
            }else {

              $field_obj = [
                "question_id" => $question->id,
                "order" => $field["order"],
                "type" => $field["type"],
                "en" => [
                  "label" => $field["label"],
                ],
              ];

              if (in_array($field["type"], [1, 2, 3, 4, 5, 6, 8, 11, 12])) {
                $field_obj["en"]["placeholder"] = $field["placeholder"];
                if (!empty($field["ar_label"]) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
                  $field_obj["ar"]["label"] = $field["ar_label"];
                  $field_obj["ar"]["placeholder"] = $field["ar_placeholder"];
                }
              }
              if ($field["type"] != 13) {
                $field_obj["is_required"] = $field["is_required"];
              }
              if ($field["type"] == 3) {
                $field_obj["is_lead_email"] = $field["is_lead_email"];
              }
              if ($field["type"] == 7) {
                $field_obj["is_multiple_chooseing"] = $field["is_multiple_chooseing"];
              }
              if ($field["type"] == 13) {
                $field_obj["hidden_value"] = $field["hidden_value"];
              }

              $new_field = Field::create($field_obj);
              array_push($fields_ids, $new_field->id);

              if (in_array($field["type"], [7, 8])) {
                $options = [];
                foreach($field["options"] as $option) {
                  array_push($options, [
                    "field_id" => $new_field->id,
                    "value" => $option,
                  ]);
                }
                $new_field->options()->createMany($options);
              }

            }
          }

          Field::where("question_id", $question->id)->whereNotIn('id', $fields_ids)->delete();

        }else if (request("type_id") == 4) {

          $question->translateOrNew("en")->title        = request("en_question_title");
          $question->translateOrNew("en")->description  = request("en_question_desc");
          $question->translateOrNew("en")->button_label = request("en_button_label");
          if (!empty(request("ar_question_title")) && !empty(request("ar_button_label")) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
            $question->translateOrNew("ar")->title        = request("ar_question_title");
            $question->translateOrNew("ar")->description  = request("ar_question_desc");
            $question->translateOrNew("ar")->button_label = request("ar_button_label");
          }

        }else if (request("type_id") == 5) {

          if (request("media_type") == "image") {
            if (!isset($request["image"]) || $request["image"] == NULL) {
              if ($question->image == NULL) {
                $validatedImage = $request->validate([
                  "image" => "required|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte",
                ]);
              }
            }else {
              if ($question->image != NULL && File::exists(public_path("images/uploads/$question->image"))) {
                File::delete(public_path("images/uploads/$question->image"));
              }
              $image = $request["image"];
              $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . $image->getClientOriginalName());
              $image->move(public_path('images/uploads'), $unique_name);
              $question->image = $unique_name;
              $question->video = NULL;
            }
          }elseif (request("media_type") == "video") {
            $question->video = $request["video"];
            if ($question->image != NULL && File::exists(public_path("images/uploads/$question->image"))) {
              File::delete(public_path("images/uploads/$question->image"));
            }
            $question->image = NULL;
          }

          $question->translateOrNew("en")->title        = request("en_question_title");
          $question->translateOrNew("en")->description  = request("en_question_desc");
          $question->translateOrNew("en")->button_label = request("en_button_label");
          if (!empty(request("ar_question_title")) && !empty(request("ar_button_label")) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
            $question->translateOrNew("ar")->title        = request("ar_question_title");
            $question->translateOrNew("ar")->description  = request("ar_question_desc");
            $question->translateOrNew("ar")->button_label = request("ar_button_label");
          }

        }

        return $question->save();

      }elseif ($question->quiz->type == 2) {// type 2 => outcome

        if (in_array(request("type_id"), [1, 2])) {
          $validate_ruls = [
            "image"           => "nullable|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte
            "video"           => "nullable|url",
            "en_question_title"  => "required|max:200",
            "en_question_desc"   => "max:400",
            "ar_question_title"  => "sometimes|required|string|max:200",
            "ar_question_desc"   => "nullable|sometimes|max:400",
            "en_answers"         => "required|array|min:2",
            "en_answers.*.id"    => "nullable|integer|exists:answers",
            "en_answers.*.text"  => "required",
            "en_answers.*.ar_text" => "sometimes|nullable",
            "en_answers.*.order" => "required|integer|min:1",
            "en_answers.*.action" => "sometimes|in:remove",
            "multi_select"    => "required|boolean",
          ];
        }else if (request("type_id") == 3) {
          $validate_ruls = [
            "media_type"      => "required|in:image,video",
            "video"           => "nullable|exclude_if:media_type,image|url",
            "image"           => "nullable|exclude_if:media_type,video|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte",
            "en_question_title" => "required|max:200",
            "en_question_desc"  => "nullable|max:400",
            "en_button_label"   => "required|string|max:20",
            "ar_question_title" => "sometimes|required|string|max:200",
            "ar_question_desc"  => "sometimes|nullable|max:400",
            "ar_button_label"   => "sometimes|string|max:20",
            "is_skippable"    => "required|in:true,false",
            "show_policy"     => "required|in:true,false",
            "fields"            => "required|array|max:16",
            "fields.*.id"       => "sometimes|required|integer|exists:fields,id",
            "fields.*.label"    => "required|string|max:120",
            "fields.*.order"    => "required|string|max:120",
            "fields.*.placeholder" => "nullable|string|max:120",
            "fields.*.type"     => "required|integer|min:1|max:13",
            "fields.*.is_multiple_chooseing" => "required_if:fields.*.type,7|integer|in:0,1",
            "fields.*.options" => "required_if:fields.*.type,7|array|min:1",
            "fields.*.options.*" => "required_if:fields.*.type,7|string|max:255",
          ];
        }else if (request("type_id") == 4) {
          $validate_ruls = [
            "en_question_title"  => "required|max:200",
            "en_question_desc"   => "max:400",
            "en_button_label"    => "required|max:20",
            "ar_question_title"  => "sometimes|required|string|max:200",
            "ar_question_desc"   => "sometimes|nullable|max:400",
            "ar_button_label"    => "sometimes|string|max:20",
          ];
        }else if (request("type_id") == 5) {
          $validate_ruls = [
            "media_type"      => "required|in:image,video",
            "video"           => "required_if:media_type,video|url",
            "image"           => "nullable|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte",
            "en_question_title"  => "required|max:200",
            "en_question_desc"   => "nullable|max:400",
            "en_button_label"    => "required|max:20",
            "ar_question_title"  => "sometimes|required|string|max:200",
            "ar_question_desc"   => "sometimes|nullable|max:400",
            "ar_button_label"    => "sometimes|string|max:20",
          ];
        }

        $request->validate($validate_ruls);

        if (in_array(request("type_id"), [1, 2])) {

          if ($request->hasFile('image')) {

            $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . $request->image->getClientOriginalName());
            $request->image->move(public_path('images/uploads'), $unique_name);
            $old_image = $question->image;
            $question->image = $unique_name;

            if (File::exists(public_path("images/uploads/") . $old_image)) {
              File::delete(public_path("images/uploads/") . $old_image);
            }
          }

          $question->video = request("video");
          $question->translateOrNew('en')->title = request("en_question_title");
          $question->translateOrNew('en')->description = request("en_question_desc");
          if (!empty(request("ar_question_title")) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
            $question->translateOrNew('ar')->title = request("ar_question_title");
            $question->translateOrNew('ar')->description = request("ar_question_desc");
          }
          $question->multi_select = $request->boolean("multi_select");

          // Answers Handlers
          $answers = $question->answers;

          if (request("type_id") == 1) {// text question

            foreach (request("en_answers") as $answer) {
              if (isset($answer["id"]) && !empty($answer["id"]) && is_numeric((int)$answer["id"])) {
                foreach ($answers as $DBAnswer) {
                  if ($DBAnswer->id == $answer["id"]) {
                    if (isset($answer["action"]) && $answer["action"] == "remove") {
                      $DBAnswer->delete();
                    }else {
                      $DBAnswer->translateOrNew("en")->text = $answer["text"];
                      if (!empty($answer["ar_text"]) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
                        $DBAnswer->translateOrNew("ar")->text = $answer["ar_text"] ?? "";
                      }
                      $DBAnswer->order = $answer["order"];
                      $DBAnswer->save();
                    }
                  }
                }
              }else {
                $createInfo = [
                  "question_id" => $question->id,
                  'en' => ['text' => $answer["text"]],
                  "order" => $answer["order"]
                ];
                if (!empty($answer["ar_text"]) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
                  $createInfo["ar"] = ['text' => $answer["ar_text"]];
                }
                Answer::create($createInfo);
              }
            }

          }elseif (request("type_id") == 2) {// image question

            foreach (request("en_answers") as $i => $answer) {

              if (isset($answer["id"]) && !empty($answer["id"]) && is_numeric((int)$answer["id"])) {
                foreach ($answers as $DBAnswer) {
                  if ($DBAnswer->id == $answer["id"]) {
                    if (isset($answer["action"]) && $answer["action"] == "remove") {
                      $DBAnswer->delete();
                      if ($DBAnswer->image != null && File::exists(public_path("images/uploads/") . $DBAnswer->image)) {
                        File::delete(public_path("images/uploads/") . $DBAnswer->image);
                      }
                    }else {
                      $DBAnswer->translateOrNew("en")->text = $answer["text"];
                      if (!empty($answer["ar_text"]) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
                        $DBAnswer->translateOrNew("ar")->text = $answer["ar_text"] ?? "";
                      }
                      $DBAnswer->order = $answer["order"];
                      if ($request->hasFile("en_answers.$i")) {
                        $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . $answer["image"]->getClientOriginalName());
                        $answer["image"]->move(public_path('images/uploads'), $unique_name);
                        $old_image = $DBAnswer->image;
                        if ($old_image != null && File::exists(public_path("images/uploads/") . $old_image)) {
                          File::delete(public_path("images/uploads/") . $old_image);
                        }
                        $DBAnswer->image = $unique_name;
                      }
                      $DBAnswer->save();
                    }
                  }
                }
              }else {

                $request->validate([
                  "en_answers.$i.image" => "required|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte
                ]);

                if ($request->hasFile("en_answers.$i")) {
                  $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . $answer["image"]->getClientOriginalName());
                  $answer["image"]->move(public_path('images/uploads'), $unique_name);
                  $createInfo = [
                    "question_id" => $question->id,
                    'en' => ['text' => $answer["text"]],
                    "order" => $answer["order"],
                    "image" => $unique_name
                  ];
                  if (!empty($answer["ar_text"]) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
                    $createInfo["ar"] = ['text' => $answer["ar_text"]];
                  }
                  Answer::create($createInfo);
                }

              }
            }

          }

        }else if (request("type_id") == 3) {

          if (request("media_type") == "video" && isset($request["video"])) {
            $question->video = $request["video"];
            $question->image = NULL;
          }elseif (request("media_type") == "image" && isset($request["image"])) {
            if ($question->image != NULL && File::exists(public_path("images/uploads/$question->image"))) {
              File::delete(public_path("images/uploads/$question->image"));
            }
            $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . $request["image"]->getClientOriginalName());
            $request["image"]->move(public_path('images/uploads'), $unique_name);
            $question->image = $unique_name;
            $question->video = NULL;
          }

          $question->translateOrNew("en")->title        = $request["en_question_title"];
          $question->translateOrNew("en")->description  = $request["en_question_desc"];
          $question->translateOrNew("en")->button_label = $request["en_button_label"];
          if (!empty(request("ar_question_title")) && !empty(request("ar_button_label")) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
            $question->translateOrNew("ar")->title        = $request["ar_question_title"];
            $question->translateOrNew("ar")->description  = $request["ar_question_desc"];
            $question->translateOrNew("ar")->button_label = $request["ar_button_label"];
          }
          $question->is_skippable = $request["is_skippable"] == "true" ? 1 : 0;
          $question->show_policy  = $request["show_policy"] == "true" ? 1 : 0;

          $fields_ids = [];
          $fields = $question->fields;
          $DbFieldsIds = $fields->pluck("id")->toArray();

          foreach (request("fields") as $field) {
            if (isset($field["id"]) && null !== $field["id"]) {
              if (in_array($field["id"], $DbFieldsIds)) {

                $value = $fields->filter(function ($obj) use ($field) {
                  return $obj->id == $field["id"];
                })->first();

                if ($value !== null && !empty($value)) {

                  $en = $value->translateOrNew("en");
                  $en->label = $field["label"];
                  $value->order = $field["order"];

                  if (in_array($value->type, [1, 2, 3, 4, 5, 6, 8, 11, 12])) {// placeholder
                    $en->placeholder = $field["placeholder"];
                  }

                  if (!empty($field["ar_label"]) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
                    $ar = $value->translateOrNew("ar");
                    $ar->label = $field["ar_label"];
                    if (in_array($value->type, [1, 2, 3, 4, 5, 6, 8, 11, 12])) {
                      $ar->placeholder = $field["ar_placeholder"];
                    }
                  }

                  if ($value->type != 13) {
                    $value->is_required = $field["is_required"];
                  }
                  if ($field["type"] == 3) {
                    $value->is_lead_email = $field["is_lead_email"];
                  }
                  if ($field["type"] == 7) {
                    $value->is_multiple_chooseing = $field["is_multiple_chooseing"];
                  }
                  if ($field["type"] == 13) {
                    $value->hidden_value = $field["hidden_value"];
                  }

                  $value->save();
                  array_push($fields_ids, $value->id);

                  if (in_array($field["type"], [7, 8])) {
                    $options = [];
                    foreach($field["options"] as $option) {
                      array_push($options, [
                        "field_id" => $value->id,
                        "value" => $option,
                      ]);
                    }
                    $value->options()->delete();
                    $value->options()->createMany($options);
                  }

                }

              }
            }else {

              $field_obj = [
                "question_id" => $question->id,
                "order" => $field["order"],
                "type" => $field["type"],
                "en" => [
                  "label" => $field["label"],
                ],
              ];

              if (in_array($field["type"], [1, 2, 3, 4, 5, 6, 8, 11, 12])) {
                $field_obj["en"]["placeholder"] = $field["placeholder"];
                if (!empty($field["ar_label"]) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
                  $field_obj["ar"]["label"] = $field["ar_label"];
                  $field_obj["ar"]["placeholder"] = $field["ar_placeholder"];
                }
              }
              if ($field["type"] != 13) {
                $field_obj["is_required"] = $field["is_required"];
              }
              if ($field["type"] == 3) {
                $field_obj["is_lead_email"] = $field["is_lead_email"];
              }
              if ($field["type"] == 7) {
                $field_obj["is_multiple_chooseing"] = $field["is_multiple_chooseing"];
              }
              if ($field["type"] == 13) {
                $field_obj["hidden_value"] = $field["hidden_value"];
              }

              $new_field = Field::create($field_obj);
              array_push($fields_ids, $new_field->id);

              if (in_array($field["type"], [7, 8])) {
                $options = [];
                foreach($field["options"] as $option) {
                  array_push($options, [
                    "field_id" => $new_field->id,
                    "value" => $option,
                  ]);
                }
                $new_field->options()->createMany($options);
              }

            }
          }

          Field::where("question_id", $question->id)->whereNotIn('id', $fields_ids)->delete();

        }else if (request("type_id") == 4) {

          $question->translate("en")->title        = request("en_question_title");
          $question->translate("en")->description  = request("en_question_desc");
          $question->translate("en")->button_label = request("en_button_label");
          if (!empty(request("ar_question_title")) && !empty(request("ar_button_label")) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
            $question->translateOrNew("ar")->title        = request("ar_question_title");
            $question->translateOrNew("ar")->description  = request("ar_question_desc");
            $question->translateOrNew("ar")->button_label = request("ar_button_label");
          }

        }else if (request("type_id") == 5) {

          if (request("media_type") == "image") {
            if (!isset($request["image"]) || $request["image"] == NULL) {
              if ($question->image == NULL) {
                $requestImage = $request->validate([
                  "image" => "required|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte",
                ]);
              }
            }else {
              if ($question->image != NULL && File::exists(public_path("images/uploads/$question->image"))) {
                File::delete(public_path("images/uploads/$question->image"));
              }
              $image = $request["image"];
              $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . $image->getClientOriginalName());
              $image->move(public_path('images/uploads'), $unique_name);
              $question->image = $unique_name;
              $question->video = NULL;
            }
          }elseif (request("media_type") == "video") {
            $question->video = $request["video"];
            if ($question->image != NULL && File::exists(public_path("images/uploads/$question->image"))) {
              File::delete(public_path("images/uploads/$question->image"));
            }
            $question->image = NULL;
          }

          $question->translate("en")->title        = $request["en_question_title"];
          $question->translate("en")->description  = $request["en_question_desc"];
          $question->translate("en")->button_label = $request["en_button_label"];
          if (!empty(request("ar_question_title")) && !empty(request("ar_button_label")) && (auth()->user()->is_admin() || auth()->user()->can_translate())) {
            $question->translateOrNew("ar")->title        = $request["ar_question_title"];
            $question->translateOrNew("ar")->description  = $request["ar_question_desc"];
            $question->translateOrNew("ar")->button_label = $request["ar_button_label"];
          }

        }

        return $question->save();

      }

    }else {
      return false;
    }

  }

  public function reorder(Quiz $quiz, Request $request) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user->id) {

      $validated = $request->validate([
        "questions" => "required|array|min:2",
        "questions.*.id" => "required|integer|exists:questions",
        "questions.*.order" => "required|integer|min:1",
      ]);

      $result = true;

      foreach ($request->input("questions") as $question_data) {
        $res = Question::where('id',$question_data["id"])->update(['order'=>$question_data["order"]]);
        if (!$res) {
          $result = false;
        }
      }

      return $result;
    }else {
      return false;
    }

  }

  public function mapped(Question $question, Request $request) {

    if (auth()->user()->is_admin() || auth()->user()->id == $question->quiz->website->user->id) {

      if ($question->quiz->type == 2) {// quiz type => outcome

        $data = [];
        $answers = $question->answers;

        foreach ($answers as $i => $answer) {
          $data[$i]["id"] = $answer->id;
          $data[$i]["order"] = $answer->order;
          $data[$i]["image"] = $answer->image;
          $data[$i]["text"] = $answer->text;
          foreach ($answer->results as $result) {
            $data[$i]["results"][] = $result->id;
          }
        }

        return [
          'answers' => $data,
          'results' => $question->quiz->results,
        ];

      }else {
        return false;
      }
    }else {
      return false;
    }

  }

  public function mapping(Question $question, Request $request) {

    if (auth()->user()->is_admin() || auth()->user()->id == $question->quiz->website->user->id) {

      if ($question->quiz->type == 2) {// quiz type => outcome

        $result = Result::where("id", $request["results"][0]["id"])->get()->first();

        $validated = $request->validate([
          "results" => "required|array",
          "results.*.id" => "required|integer|exists:results,id",
          "results.*.answers" => "array",
          "results.*.answers.*" => "required|integer|exists:answers,id",
        ]);

        $process_result = true;

        foreach ($request["results"] as $result_info) {
          $result = Result::where("id", $result_info["id"])->get()->first();
          $answers = [];
          if (isset($result_info["answers"]) && is_array($result_info["answers"]) && count($result_info["answers"]) > 0) {
            foreach ($result_info["answers"] as $answer) {
              $answers[] = $answer;
            }
          }
          $answersIds = $result->answers_for_questoin($question->id)->pluck("answers.id");
          $result->answers()->wherePivotIn("answer_id", $answersIds)->sync($answers);
        }

        return $process_result;

      }else {
        return false;
      }
    }else {
      return false;
    }

  }

  public function conditioned(Question $question, Request $request) {

    if (auth()->user()->is_admin() || auth()->user()->id == $question->quiz->website->user->id) {

      $data = [];

      foreach ($question->conditions as $i => $conditionModel) {
        $condition = [];
        $condition["id"]          = $conditionModel->id;
        $condition["question_id"] = $conditionModel->question_id;
        $condition["target_type"] = $conditionModel->target_type;
        $condition["target_id"]   = $conditionModel->target_id;
        $condition["is_on"]       = $conditionModel->is_on;
        $condition["any_or"]      = $conditionModel->any_or;
        $condition["answers"]     = $conditionModel->answers->pluck('id');

        array_push($data, $condition);
      }

      $targets = [];

      $questions = Question::where("quiz_id", $question->quiz_id)->whereNotIn("id", [$question->id])->get();
      // $questions = $question->quiz->questions->except($question->id);
      $results = $question->quiz->results;
      foreach ($results as $result) {
        $result->type += 5;
      }

      $answers = $question->answers;

      return ["question_type" => $question->type, "conditions" => $data, "answers" => $answers, "targets" => [...$questions, ...$results]];

    }else {
      return false;
    }

  }

  public function conditioning(Question $question, Request $request) {

    if (auth()->user()->is_admin() || auth()->user()->id == $question->quiz->website->user->id) {

      $validated = $request->validate([
        "conditions"          => "required|array",
        "conditions.*.id"     => "nullable|integer|exists:conditions",
        "conditions.*.delete" => "sometimes|integer|min:0|max:1",
      ]);

      $result = true;
      foreach ($request["conditions"] as $conditionInfo) {
        if (isset($conditionInfo["delete"]) && $conditionInfo["delete"] == 1) {
          $error = Condition::where('id', $conditionInfo["id"])->delete();
          if ($error == 0) {
            $result = false;
          }
        }else {

          if (in_array($question->type, [1, 2])) {

            $validated = $request->validate([
              "conditions.*.question_id"  => "required|integer",
              "conditions.*.answers"      => "required|array",
              "conditions.*.answers.*"    => "integer",
              "conditions.*.is_on"        => "required|string|in:true,false",
              "conditions.*.any_or"       => "required|boolean|in:0,1",
              "conditions.*.target_id"    => "required|integer|min:1",
              "conditions.*.target_type"  => "required|integer|min:1|max:7",
            ]);
          }else {

            $validated = $request->validate([
              "conditions.*.question_id"  => "required|integer",
              "conditions.*.is_on"        => "required|string|in:true,false",
              "conditions.*.target_id"    => "required|integer|min:1",
              "conditions.*.target_type"  => "required|integer|min:1|max:7",
            ]);
          }

          if (!empty($conditionInfo["id"])) {

            $condition = Condition::where("id", $conditionInfo["id"])->get()->first();

            if ($condition != null) {

              $condition->is_on       = $conditionInfo["is_on"] == "true" ? 1 : 0;
              $condition->target_id   = $conditionInfo["target_id"];
              $condition->target_type = $conditionInfo["target_type"];

              if (in_array($question->type, [1, 2])) {
                $condition->any_or = $conditionInfo["any_or"];
                $condition->answers()->sync($conditionInfo["answers"]);
              }

              $error = $condition->save();
              if ($error == 0) {
                $result = false;
              }
            }

          }else {

            $conditionCreateInfo = [
              "question_id" => $conditionInfo["question_id"],
              "is_on"       => $conditionInfo["is_on"] == "true" ? 1 : 0,
              "target_id"   => $conditionInfo["target_id"],
              "target_type" => $conditionInfo["target_type"],
            ];

            if (in_array($question->type, [1, 2])) {
              $conditionCreateInfo["any_or"] = $conditionInfo["any_or"];
            }

            $condition = Condition::create($conditionCreateInfo);

            if (in_array($question->type, [1, 2])) {
              $condition->answers()->sync($conditionInfo["answers"]);
            }

            $result = $condition->id ?? false;

          }

        }
      }

      return $result;

    }else {
      return false;
    }

  }

  public function destroy(Question $question) {
    if (auth()->user()->is_admin() || auth()->user()->id == $question->quiz->website->user->id) {
      if ($question->image != NULL && File::exists(public_path("images/uploads/$question->image"))) {
        File::delete(public_path("images/uploads/$question->image"));
      }
      if (!empty($question->answers) && count($question->answers) > 0) {
        foreach ($question->answers as $answer) {
          if ($answer->image != NULL && File::exists(public_path("images/uploads/$answer->image"))) {
            File::delete(public_path("images/uploads/$answer->image"));
          }
        }
      }
      return Question::where("id", "=", $question->id)->delete();
    }else {
      return false;
    }
  }
}
