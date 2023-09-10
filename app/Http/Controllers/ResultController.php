<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Quiz;
use App\Models\Result;
use Illuminate\Http\Request;

class ResultController extends Controller {

  public function store(Quiz $quiz, Request $request) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user->id) {

      $request->validate(["type_id" => "required|integer|min:1|max:3"]);

      if ($quiz->type == 1) {// type 1 => scoring

        if (request("type_id") == 1) {// type_id 1 => result builder

          $validator = Validator::make(request()->all(), [
            "min_score"     => "required|integer|lt:max_score",
            "max_score"     => "required|integer",
            "show_score"    => "required|string|in:true,false",
            "en_score_message" => "nullable|required_if:show_score,true|string|min:1|max:40",
            "en_result_title" => "required|string|max:200",
            "en_result_desc"  => "nullable|string",
            "en_button_label" => "nullable|required_if:show_buttin,true|string|min:1|max:20",
            "show_button"     => "required|string|in:true,false",
            "result_link"   => "nullable|required_if:show_buttin,true|url",
            "show_social"   => "required|string|in:true,false",
          ]);

          foreach ($quiz->results as $DBresult) {
            if (
              ($DBresult->min_score >= request("min_score") && $DBresult->min_score <= request("max_score")) ||
              ($DBresult->max_score >= request("min_score") && $DBresult->max_score <= request("max_score")) ||
              (request("min_score") >= $DBresult->min_score  && request("min_score") <= $DBresult->max_score) ||
              (request("max_score") >= $DBresult->min_score  && request("max_score") <= $DBresult->max_score)
            ) {
              $validator->errors()->add(
                'min_score', 'there is a collapse between result scores!!'
              );
            }
          }

          if (count($validator->errors()) > 0) {
            return response([
              "message" => $validator->messages()->first(),
              "errors" => $validator->errors(),
            ], 500);
            exit();
          }else {
            $resultInfo = [
              "quiz_id" => $quiz->id,
              "type" => request("type_id"),
              "en" => [
                "title" => request("en_result_title"),
                "description" => request("en_result_desc"),
                "button_label" => request("en_button_label"),
                "score_message" => request("en_score_message"),
              ],
              "show_button" => request("show_button") == "true" ? 1: 0,
              "result_link" => request("result_link"),
              "show_social" => request("show_social") == "true" ? 1: 0,
              "min_score" => request("min_score"),
              "max_score" => request("max_score"),
              "show_score" => request("show_score") == "true" ? 1: 0,
            ];
            if (auth()->user()->is_admin() && null !== request("ar_result_title")) {
              $resultInfo["ar"] = [
                "title" => request("ar_result_title"),
                "description" => request("ar_result_desc"),
                "button_label" => request("ar_button_label"),
                "score_message" => request("ar_score_message"),
              ];
            }
            $result = Result::create($resultInfo);
          }

        }elseif (request("type_id") == 2) {// type_id 2 => redirect result

          $validator = Validator::make(request()->all(), [
            "min_score" => "required|integer|lt:max_score",
            "max_score" => "required|integer",
            "en_result_title" => "required|string|max:200",
            "result_link"  => "required|url",
            "send_data"    => "required|string|in:true,false",
            "send_utm"     => "required|string|in:true,false",
          ]);

          foreach ($quiz->results as $DBresult) {
            if (
              ($DBresult->min_score >= request("min_score") && $DBresult->min_score <= request("max_score")) ||
              ($DBresult->max_score >= request("min_score") && $DBresult->max_score <= request("max_score")) ||
              (request("min_score") >= $DBresult->min_score  && request("min_score") <= $DBresult->max_score) ||
              (request("max_score") >= $DBresult->min_score  && request("max_score") <= $DBresult->max_score)
            ) {
              $validator->errors()->add(
                'min_score', 'there is a collapse between result scores!!'
              );
            }
          }

          if (count($validator->errors()) > 0) {
            return response([
              "message" => $validator->messages()->first(),
              "errors" => $validator->errors(),
            ], 500);
            exit();
          }else {
            $result = Result::create([
              "quiz_id"     => $quiz->id,
              "type"        => request("type_id"),
              "en" => [
                "title" => request("en_result_title"),
              ],
              "result_link" => request("result_link"),
              "min_score"   => request("min_score"),
              "max_score"   => request("max_score"),
              "send_data"   => request("send_data") == "true" ? 1: 0,
              "send_UTM"    => request("send_utm") == "true" ? 1: 0,
            ]);
          }

        }elseif (request("type_id") == 3) {// 3 => calendly

          $validator = Validator::make(request()->all(), [
            "min_score" => "required|integer|lt:max_score",
            "max_score" => "required|integer",
            "result_link"  => "required|string",
            "show_score"    => "required|string|in:true,false",
            "en_score_message" => "nullable|required_if:show_score,true|string|min:1|max:40",
            "en_result_title" => "required|string|max:200",
          ]);

          foreach ($quiz->results as $DBresult) {
            if (
              ($DBresult->min_score >= request("min_score") && $DBresult->min_score <= request("max_score")) ||
              ($DBresult->max_score >= request("min_score") && $DBresult->max_score <= request("max_score")) ||
              (request("min_score") >= $DBresult->min_score  && request("min_score") <= $DBresult->max_score) ||
              (request("max_score") >= $DBresult->min_score  && request("max_score") <= $DBresult->max_score)
            ) {
              $validator->errors()->add(
                'min_score', 'there is a collapse between result scores!!'
              );
            }
          }

          if (count($validator->errors()) > 0) {
            return response([
              "message" => $validator->messages()->first(),
              "errors" => $validator->errors(),
            ], 500);
            exit();
          }else {

            $resultInfo = [
              "quiz_id"     => $quiz->id,
              "type"        => request("type_id"),
              "show_score" => request("show_score"),
              "en" => [
                "title"         => request("en_result_title"),
                "description"   => request("en_result_desc"),
                "score_message"   => request("en_score_message"),
              ],
              "result_link" => request("result_link"),
              "min_score"   => request("min_score"),
              "max_score"   => request("max_score"),
            ];

            if (auth()->user()->is_admin() && request("ar_result_title") !== null) {
              $resultInfo["ar"] = [
                "title"         => request("ar_result_title"),
                "description"   => request("ar_result_desc"),
                "score_message" => request("ar_score_message"),
              ];
            }
            $result = Result::create($resultInfo);
          }
        }

        return isset($result) ? $result->id : false;

      }else if ($quiz->type == 2) {// type 2 => outcome

        if (request("type_id") == 1) {// 1 => result builder

          $validated = $request->validate([
            "en_result_title" => "required|string|max:200",
            "en_result_desc"  => "nullable|string",
            "en_button_label" => "nullable|required_if:show_buttin,true|string|min:1|max:20",
            "ar_result_title" => "nullable|string|max:200",
            "ar_result_desc"  => "nullable|string",
            "ar_button_label" => "nullable|required_if:show_buttin,true|string|min:1|max:20",
            "show_button"     => "required|string|in:true,false",
            "result_link"   => "nullable|required_if:show_buttin,true|url",
            "show_social"   => "required|string|in:true,false",
          ]);

          $resultInfo = [
            "quiz_id" => $quiz->id,
            "type"    => request("type_id"),
            "en" => [
              "title"        => request("en_result_title"),
              "description"  => request("en_result_desc"),
              "button_label" => request("en_button_label"),
            ],
            "show_button" => request("show_button") == "true" ? 1: 0,
            "result_link" => request("result_link"),
            "show_social" => request("show_social") == "true" ? 1: 0,
          ];
          if (auth()->user()->is_admin() && null !== request("ar_result_title")) {
            $resultInfo["ar"] = [
              "title" => request("ar_result_title"),
              "description" => request("ar_result_desc"),
              "button_label" => request("ar_button_label"),
            ];
          }

          $result = Result::create($resultInfo);

          if (auth()->user()->is_admin() && null !== request("ar_result_title")) {
            $ar = $result->translateOrNew("ar");
            $ar->title        = request("ar_result_title") ?? "";
            $ar->description  = request("ar_result_desc") ?? "";
            $ar->button_label = request("ar_button_label") ?? "";
            $ar->save();
          }

        }elseif (request("type_id") == 2) {// 2 => redirect result

          $validated = $request->validate([
            "en_result_title" => "required|string|max:200",
            "result_link"  => "required|url",
            "send_data"    => "required|string|in:true,false",
            "send_utm"     => "required|string|in:true,false",
          ]);

          $result = Result::create([
            "quiz_id"     => $quiz->id,
            "type"        => request("type_id"),
            "en" => [
              "title"    => request("en_result_title"),
            ],
            "result_link" => request("result_link"),
            "send_data"   => request("send_data") == "true" ? 1: 0,
            "send_UTM"    => request("send_utm") == "true" ? 1: 0,
          ]);

        }elseif (request("type_id") == 3) {// 3 => calendly

          $validated = $request->validate([
            "en_result_title" => "required|string|max:200",
            "result_link"  => "required|string",
          ]);

          $resultInfo = [
            "quiz_id"     => $quiz->id,
            "type"        => request("type_id"),
            "en" => [
              "title"         => request("en_result_title"),
              "description"   => request("en_result_desc"),
            ],
            "result_link" => request("result_link"),
          ];

          if (auth()->user()->is_admin() && request("ar_result_title") !== null) {
            $resultInfo["ar"] = [
              "title"         => request("ar_result_title"),
              "description"   => request("ar_result_desc"),
            ];
          }

          $result = Result::create($resultInfo);

        }

        return isset($result) ? $result->id : false;

      }else {// quiz type is not scoring or outcome
        return false;
      }

    }else {// not admin
      return false;
    }

  }

  public function copy(Result $result, Request $request) {

    if (auth()->user()->is_admin() || auth()->user()->id == $result->quiz->website->user->id) {

      $newResult = $result->replicateWithTranslations();

      if (!auth()->user()->is_admin() && !auth()->user()->is_sub()) {
        foreach ($newResult->translations as $key => $trans) {
          if ($trans->locale != "en") {
            $newResult->translations->forget($key);
          }
        }
      }

      $newResult->min_score = null;
      $newResult->max_score = null;

      $newResult->save();

      return $newResult->id;
    }else {
      return false;
    }

  }

  public function show(Result $result) {
    return $result;
  }

  public function update(Request $request, Result $result) {

    if (auth()->user()->is_admin() || auth()->user()->id == $result->quiz->website->user->id) {

      $request->validate(["type_id" => "required|integer|min:1|max:3"]);

      if ($result->quiz->type == 1) {// 1 => scoring

        if (request("type_id") == 1) {// 1 => result builder

          $validator = Validator::make(request()->all(), [
            "min_score"         => "required|integer|lt:max_score",
            "max_score"         => "required|integer",
            "show_score"        => "required|string|in:true,false",
            "en_score_message"  => "nullable|required_if:show_score,true|string|min:1|max:40",
            "en_result_title"   => "required|string|max:200",
            "en_result_desc"    => "nullable|string",
            "en_button_label"   => "nullable|required_if:show_buttin,true|string|min:1|max:20",
            "ar_score_message"  => "nullable",
            "ar_result_title"   => "nullable",
            "ar_result_desc"    => "nullable",
            "ar_button_label"   => "nullable",
            "show_button"       => "required|string|in:true,false",
            "result_link"       => "nullable|required_if:show_buttin,true|url",
            "show_social"       => "required|string|in:true,false",
          ]);

          foreach ($result->quiz->results as $DBresult) {
            if ($DBresult->id != $result->id && (
              ($DBresult->min_score >= request("min_score") && $DBresult->min_score <= request("max_score")) ||
              ($DBresult->max_score >= request("min_score") && $DBresult->max_score <= request("max_score")) ||
              (request("min_score") >= $DBresult->min_score  && request("min_score") <= $DBresult->max_score) ||
              (request("max_score") >= $DBresult->min_score  && request("max_score") <= $DBresult->max_score)
            )) {
              $validator->errors()->add(
                'min_score', 'there is a collapse between result scores!!'
              );
            }
          }

          if (count($validator->errors()) > 0) {
            return response([
              "message" => $validator->messages()->first(),
              "errors" => $validator->errors(),
            ], 500);
            exit();
          }else {
            $result->translateOrNew("en")->title        = request("en_result_title");
            $result->translateOrNew("en")->description  = request("en_result_desc");
            $result->translateOrNew("en")->button_label = request("en_button_label");
            $result->translateOrNew("en")->score_message = request("en_score_message");
            if (auth()->user()->is_admin() && null !== request("ar_result_title")) {
              $result->translateOrNew("ar")->title        = request("ar_result_title");
              $result->translateOrNew("ar")->description  = request("ar_result_desc");
              $result->translateOrNew("ar")->button_label = request("ar_button_label");
              $result->translateOrNew("ar")->score_message = request("ar_score_message");
            }
            $result->show_button = request("show_button") == "true" ? 1 : 0;
            $result->result_link = request("result_link");
            $result->show_social = request("show_social") == "true" ? 1 : 0;
            $result->min_score   = request("min_score");
            $result->max_score   = request("max_score");
            $result->show_score  = request("show_score") == "true" ? 1 : 0;
          }

        }elseif (request("type_id") == 2) {// 2 => redirect result

          $validator = Validator::make(request()->all(), [
            "min_score"       => "required|integer|lt:max_score",
            "max_score"       => "required|integer",
            "en_result_title" => "required|string|max:200",
            "result_link"     => "required|url",
            "send_data"       => "required|string|in:true,false",
            "send_utm"        => "required|string|in:true,false",
          ]);

          foreach ($result->quiz->results as $DBresult) {
            if ($DBresult->id != $result->id && (
              ($DBresult->min_score >= request("min_score") && $DBresult->min_score <= request("max_score")) ||
              ($DBresult->max_score >= request("min_score") && $DBresult->max_score <= request("max_score")) ||
              (request("min_score") >= $DBresult->min_score  && request("min_score") <= $DBresult->max_score) ||
              (request("max_score") >= $DBresult->min_score  && request("max_score") <= $DBresult->max_score)
            )) {
              $validator->errors()->add(
                'min_score', 'there is a collapse between result scores!!'
              );
            }
          }

          if (count($validator->errors()) > 0) {
            return response([
              "message" => $validator->messages()->first(),
              "errors" => $validator->errors(),
            ], 500);
            exit();
          }else {
            $result->translateOrNew("en")->title = $request["en_result_title"];
            $result->result_link = request("result_link");
            $result->min_score   = request("min_score");
            $result->max_score   = request("max_score");
            $result->send_data   = request("send_data") == "true" ? 1: 0;
            $result->send_UTM    = request("send_utm") == "true" ? 1: 0;
          }

        }elseif (request("type_id") == 3) {// 3 => calendly

          $validator = Validator::make(request()->all(), [
            "result_link"     => "required|string",
            "min_score"       => "required|integer|lt:max_score",
            "max_score"       => "required|integer",
            "show_score"      => "required|string|in:true,false",
            "en_score_message" => "nullable|required_if:show_score,true|string|min:1|max:40",
            "en_result_title" => "required|string|max:200",
          ]);

          foreach ($result->quiz->results as $DBresult) {
            if ($DBresult->id != $result->id && (
              ($DBresult->min_score >= request("min_score") && $DBresult->min_score <= request("max_score")) ||
              ($DBresult->max_score >= request("min_score") && $DBresult->max_score <= request("max_score")) ||
              (request("min_score") >= $DBresult->min_score  && request("min_score") <= $DBresult->max_score) ||
              (request("max_score") >= $DBresult->min_score  && request("max_score") <= $DBresult->max_score)
            )) {
              $validator->errors()->add(
                'min_score', 'there is a collapse between result scores!!'
              );
            }
          }

          if (count($validator->errors()) > 0) {
            return response([
              "message" => $validator->messages()->first(),
              "errors" => $validator->errors(),
            ], 500);
            exit();
          }else {
            $result->translateOrNew("en")->title = request("en_result_title");
            $result->translateOrNew("en")->description = request("en_result_desc");
            $result->translateOrNew("en")->score_message = request("en_score_message");
            $result->result_link = request("result_link");
            $result->min_score   = request("min_score");
            $result->max_score   = request("max_score");
            $result->show_score   = request("show_score");
            if (auth()->user()->is_admin() && request("ar_result_title") !== null) {
              $result->translateOrNew("ar")->title = request("ar_result_title");
              $result->translateOrNew("ar")->description = request("ar_result_desc");
              $result->translateOrNew("ar")->score_message = request("ar_score_message");
            }
          }
        }

        return isset($result) ? $result->save() : false;

      }else if ($result->quiz->type == 2) {// 2 => outcome

        if (request("type_id") == 1) {// 1 => result builder

          $validated = $request->validate([
            "en_result_title" => "required|string|max:200",
            "en_result_desc"  => "nullable|string",
            "en_button_label" => "nullable|required_if:show_buttin,true|string|min:1|max:20",
            "ar_result_title" => "nullable|string|max:200",
            "ar_result_desc"  => "nullable|string",
            "ar_button_label" => "nullable|required_if:show_buttin,true|string|min:1|max:20",
            "show_button"     => "required|string|in:true,false",
            "result_link"     => "nullable|required_if:show_buttin,true|url",
            "show_social"     => "required|string|in:true,false",
          ]);

          $en = $result->translateOrNew("en");
          $en->title           = request("en_result_title");
          $en->description     = request("en_result_desc");
          $en->button_label    = request("en_button_label");
          if (auth()->user()->is_admin() && request("ar_result_title") !== null) {
            $ar = $result->translateOrNew("ar");
            $ar->title        = request("ar_result_title") ?? "";
            $ar->description  = request("ar_result_desc") ?? "";
            $ar->button_label = request("ar_button_label") ?? "";
          }
          $result->show_button = request("show_button") == "true" ? 1 : 0;
          $result->result_link = request("result_link");
          $result->show_social = request("show_social") == "true" ? 1 : 0;

        }elseif (request("type_id") == 2) {// 2 => redirect result

          $validated = $request->validate([
            "en_result_title" => "required|string|max:200",
            "result_link"  => "required|url",
            "send_data"    => "required|string|in:true,false",
            "send_utm"     => "required|string|in:true,false",
          ]);

          $result->translateOrNew("en")->title = request("en_result_title");
          $result->result_link = request("result_link");
          $result->send_data   = request("send_data") == "true" ? 1: 0;
          $result->send_UTM    = request("send_utm") == "true" ? 1: 0;

        }elseif (request("type_id") == 3) {// 3 => calendly

          $validated = $request->validate([
            "en_result_title" => "required|string|max:200",
            "result_link"  => "required|string",
          ]);

          $result->translateOrNew("en")->title = request("en_result_title");
          $result->translateOrNew("en")->description = request("en_result_desc");
          $result->result_link = request("result_link");

          if (auth()->user()->is_admin() && request("ar_result_title") !== null) {
            $result->translateOrNew("ar")->title = request("ar_result_title");
            $result->translateOrNew("ar")->description = request("ar_result_desc");
          }

        }

        return isset($result) ? $result->save() : false;

      }else {// quiz type is not scoring or outcome
        return false;
      }

    }else {
      return false;
    }

  }

  public function destroy(Result $result) {
    if (auth()->user()->is_admin() || auth()->user()->id == $result->quiz->website->user->id) {
      return $result->delete();
    }else {
      return false;
    }
  }

}
