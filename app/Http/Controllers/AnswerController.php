<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Entry;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Result;
use App\Models\Submission;
use App\Models\Condition;
use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;// throwing custom validation errors


class AnswerController extends Controller {

    public function answering(Request $request, Quiz $quiz) {

      $request->validate([
        "id"  => "required|exists:questions",
        "submission_code" => "sometimes|exists:submissions,id",
        "answers"   => "sometimes|array|min:1",
        "answers.*" => "integer",
      ]);

      if (!empty(request("submission_code")) && request("submission_code") != null) {

        $entry = Entry::firstOrCreate([
          'question_id' => request("id"),
          'submission_id' => request("submission_code")
        ]);

        $entry->answers()->sync(request("answers") ?? []);

      }else {
        // check if this is the first questino on the quiz
        if ($quiz->ordered_question->first()->id == request("id") && $quiz->is_template == 0) {

          $submission = Submission::create([
            "quiz_id" => $quiz->id,
          ]);

          $return["submission_code"] = $submission->id;

          $entry = Entry::create([
            'question_id' => request("id"),
            'submission_id' => $submission->id
          ]);

          $entry->answers()->sync(request("answers") ?? []);

        }
      }

      $return["condition"] = "next";

      $conditions = Condition::where("question_id", request("id"))
          ->where("is_on", "1")// condition is on
          ->with("answers")
          ->get();

      if ($conditions->count() > 0) {
        foreach ($conditions as $cond) {

          if ($cond->answers->count() > 0) {

            $allowd_answers = $cond->answers->pluck('id')->toArray();

            if (count(request("answers")) > 0) {

              if ($cond->any_or == 0) {

                foreach (request("answers") as $answer_id) {
                  if (in_array($answer_id, $allowd_answers)) {
                    $return["condition"] = ["type" => $cond->target_type, "id" => $cond->target_id];
                  }
                }

              }else {

                $selected_answers = request("answers");
                sort($selected_answers);
                sort($allowd_answers);

                // Check for equality
                if ($selected_answers == $allowd_answers) {
                  $return["condition"] = ["type" => $cond->target_type, "id" => $cond->target_id];
                }
              }

            }

          }else {
            $return["condition"] = ["type" => $cond->target_type, "id" => $cond->target_id];
          }

        }
      }

      if ($quiz->type == 2) {// outcome

        if (request("answers") != null && count(request("answers")) > 0) {

          $results = DB::table("answer_result")
              ->select("result_id")
              ->whereIn("answer_id", request("answers"))
              ->get()->pluck("result_id")->toArray();

          if (!empty($results)) {
            $return["results"] = $results;
          }

        }

      }

      if ($quiz->is_template == 0) {
        if ($return["condition"] != "next" && !is_string($return["condition"]) && $return["condition"]["type"] > 5 && !empty(request("submission_code")) && request("submission_code") != null) {
          Submission::where('id', request("submission_code"))->update(['is_done' => 1]);
        }
      }

      if ($return["condition"] == "next") {

        if ($quiz->is_template == 0) {

          // get id of the next question
          $updadeViewsQuestion = DB::table("questions AS Y")
              ->join("questions AS X", "X.quiz_id", "=", DB::raw("Y.quiz_id AND X.order > Y.order"))
              ->where("Y.id", request("id"))
              ->orderBy("Y.order")
              ->limit(1)
              ->select("X.id")
              ->get();

          // check if next question id is not there (no next question)
          if ($updadeViewsQuestion->count() == 0 && !empty(request("submission_code")) && request("submission_code") != null) {
            // mark response (submission) as done
            Submission::where('id', request("submission_code"))->update(['is_done' => 1]);

            // $ac_integ = $quiz->website_integrations('activeCompaign')->first();// get website AC integration
            // if ($ac_integ != null && !empty($ac_integ)) {

            //   // get quiz use of AC integration
            //   $ac_integ_quiz = $quiz->integration_quiz()
            //       ->withPivot('id', 'key', 'value')
            //       ->where("integration_id", $ac_integ->id)
            //       ->where("integrations.name", 'activeCompaign')
            //       ->first();
            //   if ($ac_integ_quiz != null && !empty($ac_integ_quiz)) {
            //     $fields = $quiz->integration_ac_get_list_fields($ac_integ_quiz->pivot->value);// get active list fields
            //     $values = $quiz->integration_ac;
            //     // dd($fields, ["Quiz", "Question", "Result", "Field", "Date"], $values);

            //     if ($values != null && !empty($values) && count($values) > 0 && $fields != null && !empty($fields) && count($fields) > 0) {
            //       /*
            //         Quiz => 0
            //         Question => 1
            //         Result => 2
            //         Field => 3
            //         Date => 4
            //       */

            //       $types = ["Quiz", "Question", "Result", "Field", "Date"];
            //       $fields_types = ["First-Name" => 3, "Email" => 3, "Last-Name" => 3, "Phone" => 3];
            //       $contacts = [];

            //       foreach ($fields as $field) {
            //         $is_done = false;
            //         foreach ($values as $value) {
            //           if (!$is_done && isset($types[$value->type]) && $types[$value->type] == $field->id) {
            //             if ($types[$value->type] == "Quiz") {
            //               // get quiz title
            //               $contacts['Quiz'] = "supposed value";
            //               $is_done = true;
            //             }elseif ($types[$value->type] == "Question") {
            //               // get answer of question
            //               $contacts['Question'] = "supposed value";
            //               $is_done = true;
            //             }elseif ($types[$value->type] == "Result") {
            //               // get result title
            //               $contacts['Result'] = "supposed value";
            //               $is_done = true;
            //             }elseif ($types[$value->type] == "Date") {
            //               // now
            //               $contacts['Date'] = NOW();
            //               $is_done = true;
            //             }
            //           }elseif (!$is_done && in_array($value->type, $fields_types) && isset($types[$fields_types[$value->type]]) && $types[$fields_types[$value->type]] == $field->id && $types[$fields_types[$value->type]] == "Field") {
            //             $contacts['Field'] = $value->type;
            //             $is_done = true;
            //           }elseif(!$is_done && $value->type == $field->id) {
            //             $contacts[$value->type] = "supposed value";
            //           }

            //         }
            //       }

            //       dd($contacts);

            //       $curl = curl_init();
            //       curl_setopt_array($curl, array(
            //         CURLOPT_URL => $ac_integ_quiz->url . '/api/3/contacts',
            //         CURLOPT_RETURNTRANSFER => true,
            //         CURLOPT_ENCODING => '',
            //         CURLOPT_MAXREDIRS => 10,
            //         CURLOPT_TIMEOUT => 0,
            //         CURLOPT_FOLLOWLOCATION => true,
            //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //         CURLOPT_CUSTOMREQUEST => 'POST',
            //         CURLOPT_POSTFIELDS =>'{
            //         "contact": {
            //           "email": "quizzer@example.com",
            //           "firstName": "John",
            //           "lastName": "Doe",
            //           "phone": "7223224241"
            //           }
            //       }',
            //         CURLOPT_HTTPHEADER => array(
            //           'Api-Token: ' . $ac_integ_quiz->key,
            //           'Content-Type: application/json',
            //         ),
            //       ));
            //       $response = curl_exec($curl);
            //       curl_close($curl);

            //     }

            //   }// end use of integ in quiz (quiz integ)
            // }// end website integ (quiz->website_integrations)

          }

        }

      }

      return $return;

    }

    public function formEntry(Request $request, Quiz $quiz) {

      $request->validate([
        "id"  => "required|exists:questions",
        "submission_code"   => "sometimes|exists:submissions,id",
        "form_data"         => "sometimes|array|min:0",
        "form_data.*.id"    => "required|integer|exists:fields,id",
        "form_data.*.type"  => "required|integer|min:1|max:13",
      ]);

      if (!empty(request("submission_code")) && request("submission_code") != null) {

        $fields = [];
        $DBfields = Field::select("fields.id", "fields.is_required", "fields.is_multiple_chooseing", "fields.is_lead_email", "fields.hidden_value", "fields.type", "questions.is_skippable")
            ->join("questions", "questions.id", "=", "fields.question_id")
            ->where("fields.question_id", request("id"))
            ->where("questions.quiz_id", $quiz->id)
            ->get();

        if (count($DBfields) > 0) {
          foreach ($DBfields as $field) {

            if ($field->is_required == 1 && $field->is_skippable != null && $field->is_skippable != 1 && count(request('form_data')) > 0) {
              if (request("form_data.$field->id.value") !== null) {
                if (in_array($field->type, [1, 2, 5, 6, 9, 10, 13])) {
                  $request->validate([
                    "form_data.$field->id.value" => "required|string",
                  ]);
                }else if ($field->type == 3) {
                  $request->validate([
                    "form_data.$field->id.value" => "required|email",
                  ]);
                }else if (in_array($field->type, [4, 12])) {
                  $request->validate([
                    "form_data.$field->id.value" => "required|integer",
                  ]);
                }else if ($field->type == 7) {
                  $request->validate([
                    "form_data.$field->id.value" => "required|exists:options,id",
                  ]);
                }else if ($field->type == 8) {
                  $request->validate([
                    "form_data.$field->id.value" => "required|exist:options,id",
                  ]);
                }

                $fields[$field["id"]] = ["value" => request("form_data.$field->id.value")];

              }else {
                throw ValidationException::withMessages(["form_data.$field->id.value" => 'This Field is incorrect']);
              }
            }else {
              $fields[$field["id"]] = ["value" => request("form_data.$field->id.value") ?? null];
            }

          }
        }

        $entry = Entry::firstOrCreate([
          'question_id' => request("id"),
          'submission_id' => request("submission_code")
        ]);

        $entry->fields()->sync($fields ?? []);

      }else {
        // check if this is the first questino on the quiz
        if ($quiz->ordered_question->first()->id == request("id") && $quiz->is_template == 0) {

          $fields = [];
          $DBfields = Field::select("fields.id", "fields.is_required", "fields.is_multiple_chooseing", "fields.is_lead_email", "fields.hidden_value", "fields.type", "questions.is_skippable")
              ->join("questions", "questions.id", "=", "fields.question_id")
              ->where("fields.question_id", request("id"))
              ->where("questions.quiz_id", $quiz->id)
              ->get();

          if (count($DBfields) > 0) {
            foreach ($DBfields as $field) {

              if ($field->is_required == 1 && $field->is_skippable != null && $field->is_skippable != 1 && count(request('form_data')) > 0) {
                if (request("form_data.$field->id.value") !== null) {
                  if (in_array($field->type, [1, 2, 5, 6, 9, 10, 13])) {
                    $request->validate([
                      "form_data.$field->id.value" => "required|string",
                    ]);
                  }else if ($field->type == 3) {
                    $request->validate([
                      "form_data.$field->id.value" => "required|email",
                    ]);
                  }else if (in_array($field->type, [4, 12])) {
                    $request->validate([
                      "form_data.$field->id.value" => "required|integer",
                    ]);
                  }else if ($field->type == 7) {
                    $request->validate([
                      "form_data.$field->id.value" => "required|exists:options,id",
                    ]);
                  }else if ($field->type == 8) {
                    $request->validate([
                      "form_data.$field->id.value" => "required|exist:options,id",
                    ]);
                  }

                  $fields[$field["id"]] = ["value" => request("form_data.$field->id.value")];

                }else {
                  throw ValidationException::withMessages(["form_data.$field->id.value" => 'This Field is incorrect']);
                }
              }else {
                $fields[$field["id"]] = ["value" => request("form_data.$field->id.value") ?? null];
              }

            }
          }

          // creating and updating DB

          $submission = Submission::create([
            "quiz_id" => $quiz->id,
          ]);

          $return["submission_code"] = $submission->id;

          $entry = Entry::create([
            'question_id' => request("id"),
            'submission_id' => $submission->id
          ]);

          $entry->fields()->sync($fields ?? []);

        }

      }

      $return["condition"] = "next";

      $conditions = Condition::where("question_id", request("id"))
          ->where("is_on", "1")// condition is on
          ->with("answers")
          ->get();

      if ($conditions->count() > 0) {
        foreach ($conditions as $cond) {
          $return["condition"] = ["type" => $cond->target_type, "id" => $cond->target_id];
        }
      }

      if (
        $quiz->is_template == 0 &&
        $return["condition"] != "next" &&
        !is_string($return["condition"]) &&
        $return["condition"]["type"] > 5 &&
        !empty(request("submission_code")) &&
        request("submission_code") != null) {
        Submission::where('id', request("submission_code"))->update(['is_done' => 1]);
      }

      if ($return["condition"] == "next") {

        if ($quiz->is_template == 0) {

          $updadeViewsQuestion = DB::table("questions AS Y")
              ->join("questions AS X", "X.quiz_id", "=", DB::raw("Y.quiz_id AND X.order > Y.order"))
              ->where("Y.id", request("id"))
              ->orderBy("Y.order")
              ->limit(1)
              ->select("X.id")
              ->get();

          if ($updadeViewsQuestion->count() == 0 &&
          !empty(request("submission_code")) &&
          request("submission_code") != null) {
            Submission::where('id', request("submission_code"))->update(['is_done' => 1]);
          }

        }

      }

      return $return;

    }

}
