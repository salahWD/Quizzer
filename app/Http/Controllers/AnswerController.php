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
use App\Models\Acanswer;
use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException; // throwing custom validation errors


class AnswerController extends Controller {

  public function answering(Request $request, Quiz $quiz) {

    if ($quiz->is_template == 1) {
      $request->validate([
        "id"  => "required|exists:questions", // id of question
        "answers"   => "sometimes|array|min:1",
        "answers.*" => "integer",
      ]);
    } else {
      $request->validate([
        "id"  => "required|exists:questions", // id of question
        "submission_code" => "required|exists:submissions,id",
        "answers"   => "sometimes|array|min:1",
        "answers.*" => "integer",
      ]);
    }

    if ($quiz->is_template == 0 && request("submission_code") != null && !empty(request("submission_code"))) {

      $entry = Entry::firstOrCreate([
        'question_id' => request("id"),
        'submission_id' => request("submission_code")
      ]);

      $entry->answers()->sync(request("answers") ?? []);
    }

    $return["condition"] = "next";

    $conditions = Condition::where("question_id", request("id"))
      ->where("is_on", "1") // condition is on
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
            } else {

              $selected_answers = request("answers");
              sort($selected_answers);
              sort($allowd_answers);

              // Check for equality
              if ($selected_answers == $allowd_answers) {
                $return["condition"] = ["type" => $cond->target_type, "id" => $cond->target_id];
              }
            }
          }
        } else {
          $return["condition"] = ["type" => $cond->target_type, "id" => $cond->target_id];
        }
      }
    }

    if ($quiz->type == 2) { // outcome

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
        $nextQuestionUpdateViews = DB::table("questions AS Y")
          ->join("questions AS X", "X.quiz_id", "=", DB::raw("Y.quiz_id AND X.order > Y.order"))
          ->where("Y.id", request("id"))
          ->orderBy("Y.order")
          ->limit(1)
          ->select("X.id")
          ->get();

        // check if next question id is not there (no next question, quiz end)
        if ($nextQuestionUpdateViews->count() == 0) {
          // mark response (submission) as done
          Submission::where('id', request("submission_code"))->update(['is_done' => 1]);

          $ac_integ = $quiz->website_integrations('activeCompaign')->first(); // website AC integration
          if ($ac_integ != null && !empty($ac_integ)) {

            // quiz use of AC integration
            $ac_integ_quiz = $quiz->integration_quiz()
              ->withPivot('id', 'key', 'value')
              ->where("integration_id", $ac_integ->id)
              ->where("integrations.name", 'activeCompaign')
              ->first();
            if ($ac_integ_quiz != null && !empty($ac_integ_quiz)) {
              $fields = $quiz->integration_ac_get_list_fields($ac_integ_quiz->pivot->value); // active list fields
              $values = $quiz->integration_ac;
              // dd($fields, ["Quiz", "Question", "Result", "Field", "Date"], $values);

              if ($values != null && !empty($values) && count($values) > 0 && $fields != null && !empty($fields) && count($fields) > 0) {
                /*
                    Quiz => 0
                    Question => 1
                    Result => 2
                    Field => 3
                    Date => 4
                  */

                $types = ["Quiz", "Question", "Result", "Field", "Date"];
                $fields_types = ["First-Name" => 3, "Email" => 3, "Last-Name" => 3, "Phone" => 3];
                $contacts = [];

                foreach ($fields as $field) {
                  $is_done = false;
                  foreach ($values as $value) {
                    if (!$is_done && in_array($value->type, $types) && $types[$value->type] == $field->id) {

                      if ($types[$value->type] == "Quiz") {
                        $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                        $name[0] = strtolower($name[0]);
                        $contacts[$name] = $quiz->name;
                        $is_done = true;
                      } elseif ($types[$value->type] == "Question") {
                        $db_val = Question::where('questions.id', $value->value)
                          ->leftJoin("question_translations", "question_id", "=", "questions.id")
                          ->select("question_translations.title")
                          ->where("question_translations.locale", "en")
                          ->first();
                        $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                        $name[0] = strtolower($name[0]);
                        $contacts[$name] = $db_val->title ?? null;
                        $is_done = true;
                      } elseif ($types[$value->type] == "Result") {
                        $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                        $name[0] = strtolower($name[0]);
                        $contacts[$name] = "supposed value"; // integ result
                        $is_done = true;
                      } elseif ($types[$value->type] == "Date") {
                        $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                        $name[0] = strtolower($name[0]);
                        $contacts[$name] = NOW()->format("Y-m-d");
                        $is_done = true;
                      }
                    } elseif (
                      !$is_done &&
                      $value->type == $field->id &&
                      in_array($value->type, array_keys($fields_types)) &&
                      $types[$fields_types[$value->type]] == 'Field'
                    ) {
                      $db_val = Submission::where('submissions.id', request("submission_code"))
                        ->leftJoin("entries", "submission_id", "=", "submissions.id")
                        ->leftJoin("entry_field", "entry_id", "=", "entries.id")
                        ->where("entry_field.field_id", $value->value)
                        ->first();
                      $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                      $name[0] = strtolower($name[0]);
                      $contacts[$name] = $db_val->value ?? null;
                      $is_done = true;
                    } elseif (!$is_done && $value->type == $field->id) {

                      if (!$is_done && in_array($value->value_type, $types)) {
                        if ($value->value_type == "Quiz") {
                          $contacts["fieldValues"][] = ["field" => $value->type, "value" => $quiz->name];
                          $is_done = true;
                        } elseif ($value->value_type == "Question") {
                          $db_val = Question::where('questions.id', $value->value)
                            ->leftJoin("question_translations", "question_id", "=", "questions.id")
                            ->select("question_translations.title")
                            ->where("question_translations.locale", "en")
                            ->first();
                          $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                          $name[0] = strtolower($name[0]);
                          $contacts["fieldValues"][] = ["field" => $value->type, "value" => $db_val->title ?? null];
                          $is_done = true;
                        } elseif ($value->value_type == "Result") {
                          $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                          $name[0] = strtolower($name[0]);
                          $contacts["fieldValues"][] = ["field" => $value->type, "value" => "supposed value"]; // integ result
                          $is_done = true;
                        } elseif ($value->value_type == "Date") {
                          $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                          $name[0] = strtolower($name[0]);
                          $contacts["fieldValues"][] = ["field" => $value->type, "value" => NOW()->format("Y-m-d")];
                          $is_done = true;
                        }
                      } elseif (!$is_done && in_array($value->value_type, array_keys($fields_types))) {
                        $db_val = Submission::where('submissions.id', request("submission_code"))
                          ->leftJoin("entries", "submission_id", "=", "submissions.id")
                          ->leftJoin("entry_field", "entry_id", "=", "entries.id")
                          ->where("entry_field.field_id", $value->value)
                          ->first();
                        $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                        $name[0] = strtolower($name[0]);
                        $contacts["fieldValues"][] = ["field" => $value->type, "value" => $db_val->value ?? null];
                        $is_done = true;
                      }
                    }
                  }
                }

                // $contacts["p[" . $ac_integ_quiz->pivot->value . "]"] = intval($ac_integ_quiz->pivot->value);
                // $contact["tags"] = "tag, tag 2,";

                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => $ac_integ_quiz->value . '/api/3/contacts',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS => '{
                    "contact": ' .
                    json_encode($contacts)
                    . '}',
                  CURLOPT_HTTPHEADER => array(
                    'Api-Token: ' . $ac_integ_quiz->key,
                    'Content-Type: application/json',
                  ),
                ));
                $response = curl_exec($curl);
                $contact = $response != null ? json_decode($response) : null;
                $contact_id = $contact != null && isset($contact->contact) && $contact->contact != null ? $contact->contact->id : null;

                curl_setopt_array($curl, array(
                  CURLOPT_URL => $ac_integ_quiz->value . '/api/3/contactLists',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS => '{
                      "contactList": {
                          "list": ' . $ac_integ_quiz->pivot->value . ',
                          "contact": ' . $contact_id . ',
                          "status": 1
                      }
                    }',
                  CURLOPT_HTTPHEADER => array(
                    'Api-Token: ' . $ac_integ_quiz->key,
                    'Content-Type: application/json',
                  ),
                ));
                curl_exec($curl);

                // get tags
                // foreach on all tags and add them one by one to the contact
                // using the request code below
                $ac_answers_tags = Quiz::leftJoin("questions AS Q", "Q.quiz_id", "=", "quizzes.id")
                  ->leftJoin("answers AS A", "A.question_id", "=", "Q.id")
                  ->leftJoin("acanswers AS AC", "AC.answer_id", "=", "A.id")
                  ->select("A.id", "AC.tag")
                  ->where("quizzes.id", "=", $quiz->id)
                  ->whereNotNull("AC.id")
                  ->get();

                if (!empty($ac_answers_tags) && count($ac_answers_tags) > 0) {
                  $ac_answers_tags = collect($ac_answers_tags)->keyBy('id')->toArray();
                  $submission_answers = Submission::where('submissions.id', request("submission_code"))
                    ->leftJoin("entries", "submission_id", "=", "submissions.id")
                    ->leftJoin("answer_entry", "entry_id", "=", "entries.id")
                    ->select("answer_entry.answer_id")
                    ->whereNotNull("answer_entry.answer_id")
                    ->get()->toArray();
                  $submission_answers = array_column($submission_answers, "answer_id");
                  if (!empty($submission_answers) && count($submission_answers) > 0) {
                    foreach ($submission_answers as $answer) {
                      if (in_array($answer, array_keys($ac_answers_tags))) {
                        curl_setopt_array($curl, array(
                          CURLOPT_URL => $ac_integ_quiz->value . '/api/3/contactTags',
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_ENCODING => '',
                          CURLOPT_MAXREDIRS => 10,
                          CURLOPT_TIMEOUT => 0,
                          CURLOPT_FOLLOWLOCATION => true,
                          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                          CURLOPT_CUSTOMREQUEST => 'POST',
                          CURLOPT_POSTFIELDS => '{
                              "contactTag": {
                                "contact": "' . $contact_id . '",
                                "tag": "' . $ac_answers_tags[$answer]["tag"] . '"
                              }
                            }',
                          CURLOPT_HTTPHEADER => array(
                            'Api-Token: ' . $ac_integ_quiz->key,
                            'Content-Type: application/json',
                          ),
                        ));
                        $res = curl_exec($curl);
                      }
                    }
                  }
                }
                curl_close($curl);
              }
            }
          } // end Active Campaign

          $gr_integ = $quiz->website_integrations('getResponse')->first(); // website GR integration
          if ($gr_integ != null && !empty($gr_integ)) {

            // quiz use of GR integration (acive list)
            $gr_integ_quiz = $quiz->integration_quiz()
              ->withPivot('id', 'key', 'value')
              ->where("integration_id", $gr_integ->id)
              ->where("integrations.name", 'getResponse')
              ->first();
            if ($gr_integ_quiz != null && !empty($gr_integ_quiz)) {
              $fields = $quiz->integration_gr_get_list_fields(); // custom fields
              $values = $quiz->integration_gr;

              if ($values != null && !empty($values) && count($values) > 0 && $fields != null && !empty($fields) && count($fields) > 0) {
                //Quiz => 0
                //Question => 1
                //Result => 2
                //Field => 3
                //Date => 4

                $types = ["Quiz", "Question", "Result", "Field", "Date"];
                $fields_types = ["First-Name" => 3, "Email" => 3, "Last-Name" => 3, "Phone" => 3];
                $contacts = [];

                foreach ($fields as $field) {
                  $is_done = false;
                  foreach ($values as $value) {
                    if (!$is_done && in_array($value->type, $types) && $types[$value->type] == $field->customFieldId) {

                      if ($types[$value->type] == "Quiz") {
                        $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                        $name[0] = strtolower($name[0]);
                        $contacts[$name] = $quiz->name;
                        $is_done = true;
                      } elseif ($types[$value->type] == "Question") {
                        $db_val = Question::where('questions.id', $value->value)
                          ->leftJoin("question_translations", "question_id", "=", "questions.id")
                          ->select("question_translations.title")
                          ->where("question_translations.locale", "en")
                          ->first();
                        $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                        $name[0] = strtolower($name[0]);
                        $contacts[$name] = $db_val->title ?? null;
                        $is_done = true;
                      } elseif ($types[$value->type] == "Result") {
                        $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                        $name[0] = strtolower($name[0]);
                        $contacts[$name] = "supposed value"; // integ result
                        $is_done = true;
                      } elseif ($types[$value->type] == "Date") {
                        $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                        $name[0] = strtolower($name[0]);
                        $contacts[$name] = NOW()->format("Y-m-d");
                        $is_done = true;
                      }
                    } elseif (
                      !$is_done &&
                      $value->type == $field->customFieldId &&
                      in_array($value->type, array_keys($fields_types)) &&
                      $types[$fields_types[$value->type]] == 'Field'
                    ) {
                      $db_val = Submission::where('submissions.id', request("submission_code"))
                        ->leftJoin("entries", "submission_id", "=", "submissions.id")
                        ->leftJoin("entry_field", "entry_id", "=", "entries.id")
                        ->where("entry_field.field_id", $value->value)
                        ->first();
                      $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                      $name[0] = strtolower($name[0]);
                      $contacts[$name] = $db_val->value ?? null;
                      $is_done = true;
                    } elseif (!$is_done && $value->type == $field->customFieldId) {

                      if (!$is_done && in_array($value->value_type, $types)) {
                        if ($value->value_type == "Quiz") {
                          if ($value->type == "Name") {
                            $contacts["name"] = $quiz->name;
                          } else {
                            $contacts["customFieldValues"][] = ["customFieldId" => $value->type, "value" => [$quiz->name]];
                          }
                          $is_done = true;
                        } elseif ($value->value_type == "Question") {
                          $db_val = Question::where('questions.id', $value->value)
                            ->leftJoin("question_translations", "question_id", "=", "questions.id")
                            ->select("question_translations.title")
                            ->where("question_translations.locale", "en")
                            ->first();
                          if ($value->type == "Name") {
                            $contacts["name"] = $db_val->title ?? null;
                          } else {
                            $contacts["customFieldValues"][] = ["customFieldId" => $value->type, "value" => [$db_val->title ?? null]];
                          }
                          $is_done = true;
                        } elseif ($value->value_type == "Result") {
                          if ($value->type == "Name") {
                            $contacts["name"] = "supposed value";
                          } else {
                            $contacts["customFieldValues"][] = ["customFieldId" => $value->type, "value" => ["supposed value"]];
                          }
                          $is_done = true;
                        } elseif ($value->value_type == "Date") {
                          if ($value->type == "Name") {
                            $contacts["name"] = NOW()->format("Y-m-d");
                          } else {
                            $contacts["customFieldValues"][] = ["customFieldId" => $value->type, "value" => [NOW()->format("Y-m-d")]];
                          }
                          $is_done = true;
                        }
                      } elseif (!$is_done && in_array($value->value_type, array_keys($fields_types))) {
                        $db_val = Submission::where('submissions.id', request("submission_code"))
                          ->leftJoin("entries", "submission_id", "=", "submissions.id")
                          ->leftJoin("entry_field", "entry_id", "=", "entries.id")
                          ->where("entry_field.field_id", $value->value)
                          ->first();
                        if ($value->type == "Name") {
                          $contacts["name"] = $db_val->value ?? null;
                        } else {
                          $contacts["customFieldValues"][] = ["customFieldId" => $value->type, "value" => [$db_val->value ?? null]];
                        }
                        $is_done = true;
                      }
                    }
                  }
                }

                $tags = [];
                $gr_answers_tags = Quiz::leftJoin("questions AS Q", "Q.quiz_id", "=", "quizzes.id")
                  ->leftJoin("answers AS A", "A.question_id", "=", "Q.id")
                  ->leftJoin("granswers AS GR", "GR.answer_id", "=", "A.id")
                  ->select("A.id", "GR.tag")
                  ->where("quizzes.id", "=", $quiz->id)
                  ->whereNotNull("GR.id")
                  ->get();

                if (!empty($gr_answers_tags) && count($gr_answers_tags) > 0) {
                  $gr_answers_tags = collect($gr_answers_tags)->keyBy('id')->toArray();
                  $submission_answers = Submission::where('submissions.id', request("submission_code"))
                    ->leftJoin("entries", "submission_id", "=", "submissions.id")
                    ->leftJoin("answer_entry", "entry_id", "=", "entries.id")
                    ->select("answer_entry.answer_id")
                    ->whereNotNull("answer_entry.answer_id")
                    ->get()->toArray();
                  $submission_answers = array_column($submission_answers, "answer_id");
                  if (!empty($submission_answers) && count($submission_answers) > 0) {
                    foreach ($submission_answers as $answer) {
                      if (in_array($answer, array_keys($gr_answers_tags))) {
                        $tags[] = ["tagId" => $gr_answers_tags[$answer]["tag"]];
                      }
                    }
                  }
                }
                $contacts["campaign"] = ["campaignId" => $gr_integ_quiz->pivot->value];
                $contacts["tags"] = json_encode($tags);

                $curl = curl_init();
                $res = curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://api.getresponse.com/v3/contacts',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS => json_encode($contacts),
                  CURLOPT_HTTPHEADER => array(
                    'X-Auth-Token: api-key ' . $gr_integ->key,
                  ),
                ));
                curl_close($curl);
              }
            } // end use of integ in quiz (quiz integ)
          } // end website integ (quiz->website_integrations)

          // ====== Convert Kit Api ======
          $ck_integ = $quiz->website_integrations('convertKit')->first(); // website CK integration
          if ($ck_integ != null && !empty($ck_integ)) {

            // quiz use of AC integration
            $ck_integ_quiz = $quiz->integration_quiz()
              ->withPivot('id', 'key', 'value')
              ->where("integration_id", $ck_integ->id)
              ->where("integrations.name", 'convertKit')
              ->first();
            if ($ck_integ_quiz != null && !empty($ck_integ_quiz)) {

              $fields = $quiz->integration_ck_get_list_fields(); // all custom and basic fields
              $values = $quiz->integration_ck;
              // dump($fields);
              // dd($values);
              // dd($fields, ["Quiz", "Question", "Result", "Field", "Date"], $values);

              if ($values != null && !empty($values) && count($values) > 0 && $fields != null && !empty($fields) && count($fields) > 0) {
                // Quiz => 0
                // Question => 1
                // Result => 2
                // Field => 3
                // Date => 4

                $types = ["Quiz", "Question", "Result", "Field", "Date"];
                $fields_types = ["First-Name" => 3, "Email" => 3, "Last-Name" => 3, "Phone" => 3];
                $contacts = [
                  "api_key" => $ck_integ->value,
                  "tags" => [],
                ];

                foreach ($fields as $field) {
                  $is_done = false;
                  foreach ($values as $value) {
                    // if ($value->type == $field->id) {
                    // dd(!$is_done, in_array($value->value_type, $types), !$is_done && in_array($value->value_type, $types), $value,$value->value_type,$types);
                    // }
                    // dd(collect($values)->pluck("type"), collect($fields)->pluck("id"));
                    if (!$is_done && in_array($value->type, ["Email", "First-Name"]) && $value->type == $field->id) {
                      if ($value->value_type == "Quiz") {
                        $name = strtolower(str_replace('-', '_', $value->type));
                        $contacts[$name] = $quiz->name;
                        $is_done = true;
                      } elseif ($value->value_type == "Question") {
                        $db_val = Question::where('questions.id', $value->value)
                          ->leftJoin("question_translations", "question_id", "=", "questions.id")
                          ->select("question_translations.title")
                          ->where("question_translations.locale", "en")
                          ->first();
                        $name = strtolower(str_replace('-', '_', $value->type));
                        $contacts[$name] = $db_val->title ?? null;
                        $is_done = true;
                      } elseif ($value->value_type == "Result") {
                        $name = strtolower(str_replace('-', '_', $value->type));
                        $contacts[$name] = "supposed value"; // integ result
                        $is_done = true;
                      } elseif ($value->value_type == "Date") {
                        $name = strtolower(str_replace('-', '_', $value->type));
                        $contacts[$name] = NOW()->format("Y-m-d");
                        $is_done = true;
                      } elseif ($value->value_type == "Field") {
                        $name = strtolower(str_replace('-', '_', $value->type));
                        $db_val = Submission::where('submissions.id', request("submission_code"))
                          ->leftJoin("entries", "submission_id", "=", "submissions.id")
                          ->leftJoin("entry_field", "entry_id", "=", "entries.id")
                          ->where("entry_field.field_id", $value->value)
                          ->first();
                        $contacts[$name] = $db_val->value ?? null;
                        $is_done = true;
                      }
                    } elseif (
                      !$is_done &&
                      $value->type == $field->id &&
                      in_array($value->value_type, array_keys($fields_types))
                    ) {
                      $db_val = Submission::where('submissions.id', request("submission_code"))
                        ->leftJoin("entries", "submission_id", "=", "submissions.id")
                        ->leftJoin("entry_field", "entry_id", "=", "entries.id")
                        ->where("entry_field.field_id", $value->value)
                        ->first();
                      $name = strtolower(str_replace('-', '_', $value->type));
                      $contacts[$name] = $db_val->value ?? null;
                      $is_done = true;
                    } elseif (!$is_done && $value->type == $field->id) {
                      if (!$is_done && in_array($value->value_type, $types)) {
                        if ($value->value_type == "Quiz") {
                          $contacts["fields"][$value->type] = $quiz->name;
                          $is_done = true;
                        } elseif ($value->value_type == "Question") {
                          $db_val = Question::where('questions.id', $value->value)
                            ->leftJoin("question_translations", "question_id", "=", "questions.id")
                            ->select("question_translations.title")
                            ->where("question_translations.locale", "en")
                            ->first();
                          $contacts["fields"][$value->type] = $db_val->title ?? null;
                          $is_done = true;
                        } elseif ($value->value_type == "Result") {
                          $contacts["fields"][$value->type] = "supposed value"; // integ result
                          $is_done = true;
                        } elseif ($value->value_type == "Date") {
                          $contacts["fields"][$value->type] = NOW()->format("Y-m-d");
                          $is_done = true;
                        }
                      } elseif (!$is_done && in_array($value->value_type, array_keys($fields_types))) {
                        $db_val = Submission::where('submissions.id', request("submission_code"))
                          ->leftJoin("entries", "submission_id", "=", "submissions.id")
                          ->leftJoin("entry_field", "entry_id", "=", "entries.id")
                          ->where("entry_field.field_id", $value->value)
                          ->first();
                        $contacts["fields"][$value->type] = $db_val->value ?? null;
                        $is_done = true;
                      }
                    }
                  }
                }

                $ck_answers_tags = Quiz::leftJoin("questions AS Q", "Q.quiz_id", "=", "quizzes.id")
                  ->leftJoin("answers AS A", "A.question_id", "=", "Q.id")
                  ->leftJoin("ckanswers AS CK", "CK.answer_id", "=", "A.id")
                  ->select("A.id", "CK.tag")
                  ->where("quizzes.id", "=", $quiz->id)
                  ->whereNotNull("CK.id")
                  ->get();

                if (!empty($ck_answers_tags) && count($ck_answers_tags) > 0) {
                  $ck_answers_tags = collect($ck_answers_tags)->keyBy('id')->toArray();

                  $submission_answers = Submission::where('submissions.id', request("submission_code"))
                    ->leftJoin("entries", "submission_id", "=", "submissions.id")
                    ->leftJoin("answer_entry", "entry_id", "=", "entries.id")
                    ->select("answer_entry.answer_id")
                    ->whereNotNull("answer_entry.answer_id")
                    ->get()->toArray();
                  $submission_answers = array_column($submission_answers, "answer_id");
                  if (!empty($submission_answers) && count($submission_answers) > 0) {
                    foreach ($submission_answers as $answer) {
                      if (in_array($answer, array_keys($ck_answers_tags))) {
                        array_push($contacts["tags"], $ck_answers_tags[$answer]["tag"]);
                      }
                    }
                  }
                }

                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://api.convertkit.com/v3/sequences/' . $ck_integ_quiz->pivot->value . '/subscribe',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS => json_encode($contacts),
                  CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json; charset=utf-8'
                  ),
                ));

                $response = curl_exec($curl);

                $contact = $response != null ? json_decode($response) : null;
              }
            } // end use of integ in quiz (quiz integ)
          } // end website integ (quiz->website_integrations)

        }
      }
    }

    return $return;
  }

  public function formEntry(Request $request, Quiz $quiz) {

    if ($quiz->is_template == 1) {
      $request->validate([
        "id"  => "required|exists:questions",
        "form_data"         => "sometimes|array|min:0",
        "form_data.*.id"    => "required|integer|exists:fields,id",
        "form_data.*.type"  => "required|integer|min:1|max:13",
      ]);
    } else {
      $request->validate([
        "id"  => "required|exists:questions",
        "submission_code"   => "required|exists:submissions,id",
        "form_data"         => "sometimes|array|min:0",
        "form_data.*.id"    => "required|integer|exists:fields,id",
        "form_data.*.type"  => "required|integer|min:1|max:13",
      ]);
    }

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
              } else if ($field->type == 3) {
                $request->validate([
                  "form_data.$field->id.value" => "required|email",
                ]);
              } else if (in_array($field->type, [4, 12])) {
                // dd(request("form_data.$field->id.value"));
                $request->validate([
                  "form_data.$field->id.value" => "required|regex:/[\+0-9]/i|digits_between:1,20",
                ]);
              } else if ($field->type == 7) {
                $request->validate([
                  "form_data.$field->id.value" => "required|exists:options,id",
                ]);
              } else if ($field->type == 8) {
                $request->validate([
                  "form_data.$field->id.value" => "required|exist:options,id",
                ]);
              }

              $fields[$field["id"]] = ["value" => request("form_data.$field->id.value")];
            } else {
              throw ValidationException::withMessages(["form_data.$field->id.value" => 'This Field is incorrect']);
            }
          } else {
            $fields[$field["id"]] = ["value" => request("form_data.$field->id.value") ?? null];
          }
        }
      }

      $entry = Entry::firstOrCreate([
        'question_id' => request("id"),
        'submission_id' => request("submission_code")
      ]);

      $entry->fields()->sync($fields ?? []);

      /* }else {
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
                    // dd(request("form_data.$field->id.value"));
                    $request->validate([
                      "form_data.$field->id.value" => "required|regex:/[\+0-9]/i|digits_between:1,20",
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

      } */
    }

    $return["condition"] = "next";

    $conditions = Condition::where("question_id", request("id"))
      ->where("is_on", "1") // condition is on
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
      request("submission_code") != null
    ) {
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

        if (
          $updadeViewsQuestion->count() == 0 &&
          !empty(request("submission_code")) &&
          request("submission_code") != null
        ) {

          Submission::where('id', request("submission_code"))->update(['is_done' => 1]);

          $ac_integ = $quiz->website_integrations('activeCompaign')->first(); // website AC integration
          if ($ac_integ != null && !empty($ac_integ)) {

            // quiz use of AC integration
            $ac_integ_quiz = $quiz->integration_quiz()
              ->withPivot('id', 'key', 'value')
              ->where("integration_id", $ac_integ->id)
              ->where("integrations.name", 'activeCompaign')
              ->first();
            if ($ac_integ_quiz != null && !empty($ac_integ_quiz)) {
              $fields = $quiz->integration_ac_get_list_fields($ac_integ_quiz->pivot->value); // active list fields
              $values = $quiz->integration_ac;
              // dd($fields, ["Quiz", "Question", "Result", "Field", "Date"], $values);

              if ($values != null && !empty($values) && count($values) > 0 && $fields != null && !empty($fields) && count($fields) > 0) {
                /*
                    Quiz => 0
                    Question => 1
                    Result => 2
                    Field => 3
                    Date => 4
                  */

                $types = ["Quiz", "Question", "Result", "Field", "Date"];
                $fields_types = ["First-Name" => 3, "Email" => 3, "Last-Name" => 3, "Phone" => 3];
                $contacts = [];

                foreach ($fields as $field) {
                  $is_done = false;
                  foreach ($values as $value) {
                    if (!$is_done && in_array($value->type, $types) && $types[$value->type] == $field->id) {

                      if ($types[$value->type] == "Quiz") {
                        $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                        $name[0] = strtolower($name[0]);
                        $contacts[$name] = $quiz->name;
                        $is_done = true;
                      } elseif ($types[$value->type] == "Question") {
                        $db_val = Question::where('questions.id', $value->value)
                          ->leftJoin("question_translations", "question_id", "=", "questions.id")
                          ->select("question_translations.title")
                          ->where("question_translations.locale", "en")
                          ->first();
                        $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                        $name[0] = strtolower($name[0]);
                        $contacts[$name] = $db_val->title ?? null;
                        $is_done = true;
                      } elseif ($types[$value->type] == "Result") {
                        $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                        $name[0] = strtolower($name[0]);
                        $contacts[$name] = "supposed value"; // integ result
                        $is_done = true;
                      } elseif ($types[$value->type] == "Date") {
                        $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                        $name[0] = strtolower($name[0]);
                        $contacts[$name] = NOW()->format("Y-m-d");
                        $is_done = true;
                      }
                    } elseif (
                      !$is_done &&
                      $value->type == $field->id &&
                      in_array($value->type, array_keys($fields_types)) &&
                      $types[$fields_types[$value->type]] == 'Field'
                    ) {
                      $db_val = Submission::where('submissions.id', request("submission_code"))
                        ->leftJoin("entries", "submission_id", "=", "submissions.id")
                        ->leftJoin("entry_field", "entry_id", "=", "entries.id")
                        ->where("entry_field.field_id", $value->value)
                        ->first();
                      $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                      $name[0] = strtolower($name[0]);
                      $contacts[$name] = $db_val->value ?? null;
                      $is_done = true;
                    } elseif (!$is_done && $value->type == $field->id) {

                      if (!$is_done && in_array($value->value_type, $types)) {
                        if ($value->value_type == "Quiz") {
                          $contacts["fieldValues"][] = ["field" => $value->type, "value" => $quiz->name];
                          $is_done = true;
                        } elseif ($value->value_type == "Question") {
                          $db_val = Question::where('questions.id', $value->value)
                            ->leftJoin("question_translations", "question_id", "=", "questions.id")
                            ->select("question_translations.title")
                            ->where("question_translations.locale", "en")
                            ->first();
                          $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                          $name[0] = strtolower($name[0]);
                          $contacts["fieldValues"][] = ["field" => $value->type, "value" => $db_val->title ?? null];
                          $is_done = true;
                        } elseif ($value->value_type == "Result") {
                          $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                          $name[0] = strtolower($name[0]);
                          $contacts["fieldValues"][] = ["field" => $value->type, "value" => "supposed value"]; // integ result
                          $is_done = true;
                        } elseif ($value->value_type == "Date") {
                          $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                          $name[0] = strtolower($name[0]);
                          $contacts["fieldValues"][] = ["field" => $value->type, "value" => NOW()->format("Y-m-d")];
                          $is_done = true;
                        }
                      } elseif (!$is_done && in_array($value->value_type, array_keys($fields_types))) {
                        $db_val = Submission::where('submissions.id', request("submission_code"))
                          ->leftJoin("entries", "submission_id", "=", "submissions.id")
                          ->leftJoin("entry_field", "entry_id", "=", "entries.id")
                          ->where("entry_field.field_id", $value->value)
                          ->first();
                        $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                        $name[0] = strtolower($name[0]);
                        $contacts["fieldValues"][] = ["field" => $value->type, "value" => $db_val->value ?? null];
                        $is_done = true;
                      }
                    }
                  }
                }

                // $contacts["p[" . $ac_integ_quiz->pivot->value . "]"] = intval($ac_integ_quiz->pivot->value);
                // $contact["tags"] = "tag, tag 2,";

                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => $ac_integ_quiz->value . '/api/3/contacts',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS => '{
                    "contact": ' .
                    json_encode($contacts)
                    . '}',
                  CURLOPT_HTTPHEADER => array(
                    'Api-Token: ' . $ac_integ_quiz->key,
                    'Content-Type: application/json',
                  ),
                ));
                $response = curl_exec($curl);
                $contact = $response != null ? json_decode($response) : null;
                $contact_id = $contact != null && isset($contact->contact) && $contact->contact != null ? $contact->contact->id : null;

                curl_setopt_array($curl, array(
                  CURLOPT_URL => $ac_integ_quiz->value . '/api/3/contactLists',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS => '{
                      "contactList": {
                          "list": ' . $ac_integ_quiz->pivot->value . ',
                          "contact": ' . $contact_id . ',
                          "status": 1
                      }
                    }',
                  CURLOPT_HTTPHEADER => array(
                    'Api-Token: ' . $ac_integ_quiz->key,
                    'Content-Type: application/json',
                  ),
                ));
                curl_exec($curl);

                // get tags
                // foreach on all tags and add them one by one to the contact
                // using the request code below
                $ac_answers_tags = Quiz::leftJoin("questions AS Q", "Q.quiz_id", "=", "quizzes.id")
                  ->leftJoin("answers AS A", "A.question_id", "=", "Q.id")
                  ->leftJoin("acanswers AS AC", "AC.answer_id", "=", "A.id")
                  ->select("A.id", "AC.tag")
                  ->where("quizzes.id", "=", $quiz->id)
                  ->whereNotNull("AC.id")
                  ->get();

                if (!empty($ac_answers_tags) && count($ac_answers_tags) > 0) {
                  $ac_answers_tags = collect($ac_answers_tags)->keyBy('id')->toArray();
                  $submission_answers = Submission::where('submissions.id', request("submission_code"))
                    ->leftJoin("entries", "submission_id", "=", "submissions.id")
                    ->leftJoin("answer_entry", "entry_id", "=", "entries.id")
                    ->select("answer_entry.answer_id")
                    ->whereNotNull("answer_entry.answer_id")
                    ->get()->toArray();
                  $submission_answers = array_column($submission_answers, "answer_id");
                  if (!empty($submission_answers) && count($submission_answers) > 0) {
                    foreach ($submission_answers as $answer) {
                      if (in_array($answer, array_keys($ac_answers_tags))) {
                        curl_setopt_array($curl, array(
                          CURLOPT_URL => $ac_integ_quiz->value . '/api/3/contactTags',
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_ENCODING => '',
                          CURLOPT_MAXREDIRS => 10,
                          CURLOPT_TIMEOUT => 0,
                          CURLOPT_FOLLOWLOCATION => true,
                          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                          CURLOPT_CUSTOMREQUEST => 'POST',
                          CURLOPT_POSTFIELDS => '{
                              "contactTag": {
                                "contact": "' . $contact_id . '",
                                "tag": "' . $ac_answers_tags[$answer]["tag"] . '"
                              }
                            }',
                          CURLOPT_HTTPHEADER => array(
                            'Api-Token: ' . $ac_integ_quiz->key,
                            'Content-Type: application/json',
                          ),
                        ));
                        $res = curl_exec($curl);
                      }
                    }
                  }
                }
                curl_close($curl);
              }
            }
          } // end Active Campaign

          $gr_integ = $quiz->website_integrations('getResponse')->first(); // website GR integration
          if ($gr_integ != null && !empty($gr_integ)) {

            // quiz use of GR integration (acive list)
            $gr_integ_quiz = $quiz->integration_quiz()
              ->withPivot('id', 'key', 'value')
              ->where("integration_id", $gr_integ->id)
              ->where("integrations.name", 'getResponse')
              ->first();
            if ($gr_integ_quiz != null && !empty($gr_integ_quiz)) {
              $fields = $quiz->integration_gr_get_list_fields(); // custom fields
              $values = $quiz->integration_gr;

              if ($values != null && !empty($values) && count($values) > 0 && $fields != null && !empty($fields) && count($fields) > 0) {
                //Quiz => 0
                //Question => 1
                //Result => 2
                //Field => 3
                //Date => 4

                $types = ["Quiz", "Question", "Result", "Field", "Date"];
                $fields_types = ["First-Name" => 3, "Email" => 3, "Last-Name" => 3, "Phone" => 3];
                $contacts = [];

                foreach ($fields as $field) {
                  $is_done = false;
                  foreach ($values as $value) {
                    if (!$is_done && in_array($value->type, $types) && $types[$value->type] == $field->customFieldId) {

                      if ($types[$value->type] == "Quiz") {
                        $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                        $name[0] = strtolower($name[0]);
                        $contacts[$name] = $quiz->name;
                        $is_done = true;
                      } elseif ($types[$value->type] == "Question") {
                        $db_val = Question::where('questions.id', $value->value)
                          ->leftJoin("question_translations", "question_id", "=", "questions.id")
                          ->select("question_translations.title")
                          ->where("question_translations.locale", "en")
                          ->first();
                        $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                        $name[0] = strtolower($name[0]);
                        $contacts[$name] = $db_val->title ?? null;
                        $is_done = true;
                      } elseif ($types[$value->type] == "Result") {
                        $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                        $name[0] = strtolower($name[0]);
                        $contacts[$name] = "supposed value"; // integ result
                        $is_done = true;
                      } elseif ($types[$value->type] == "Date") {
                        $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                        $name[0] = strtolower($name[0]);
                        $contacts[$name] = NOW()->format("Y-m-d");
                        $is_done = true;
                      }
                    } elseif (
                      !$is_done &&
                      $value->type == $field->customFieldId &&
                      in_array($value->type, array_keys($fields_types)) &&
                      $types[$fields_types[$value->type]] == 'Field'
                    ) {
                      $db_val = Submission::where('submissions.id', request("submission_code"))
                        ->leftJoin("entries", "submission_id", "=", "submissions.id")
                        ->leftJoin("entry_field", "entry_id", "=", "entries.id")
                        ->where("entry_field.field_id", $value->value)
                        ->first();
                      $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $value->type)));
                      $name[0] = strtolower($name[0]);
                      $contacts[$name] = $db_val->value ?? null;
                      $is_done = true;
                    } elseif (!$is_done && $value->type == $field->customFieldId) {

                      if (!$is_done && in_array($value->value_type, $types)) {
                        if ($value->value_type == "Quiz") {
                          if ($value->type == "Name") {
                            $contacts["name"] = $quiz->name;
                          } else {
                            $contacts["customFieldValues"][] = ["customFieldId" => $value->type, "value" => [$quiz->name]];
                          }
                          $is_done = true;
                        } elseif ($value->value_type == "Question") {
                          $db_val = Question::where('questions.id', $value->value)
                            ->leftJoin("question_translations", "question_id", "=", "questions.id")
                            ->select("question_translations.title")
                            ->where("question_translations.locale", "en")
                            ->first();
                          if ($value->type == "Name") {
                            $contacts["name"] = $db_val->title ?? null;
                          } else {
                            $contacts["customFieldValues"][] = ["customFieldId" => $value->type, "value" => [$db_val->title ?? null]];
                          }
                          $is_done = true;
                        } elseif ($value->value_type == "Result") {
                          if ($value->type == "Name") {
                            $contacts["name"] = "supposed value";
                          } else {
                            $contacts["customFieldValues"][] = ["customFieldId" => $value->type, "value" => ["supposed value"]];
                          }
                          $is_done = true;
                        } elseif ($value->value_type == "Date") {
                          if ($value->type == "Name") {
                            $contacts["name"] = NOW()->format("Y-m-d");
                          } else {
                            $contacts["customFieldValues"][] = ["customFieldId" => $value->type, "value" => [NOW()->format("Y-m-d")]];
                          }
                          $is_done = true;
                        }
                      } elseif (!$is_done && in_array($value->value_type, array_keys($fields_types))) {
                        $db_val = Submission::where('submissions.id', request("submission_code"))
                          ->leftJoin("entries", "submission_id", "=", "submissions.id")
                          ->leftJoin("entry_field", "entry_id", "=", "entries.id")
                          ->where("entry_field.field_id", $value->value)
                          ->first();
                        if ($value->type == "Name") {
                          $contacts["name"] = $db_val->value ?? null;
                        } else {
                          $contacts["customFieldValues"][] = ["customFieldId" => $value->type, "value" => [$db_val->value ?? null]];
                        }
                        $is_done = true;
                      }
                    }
                  }
                }

                $tags = [];
                $gr_answers_tags = Quiz::leftJoin("questions AS Q", "Q.quiz_id", "=", "quizzes.id")
                  ->leftJoin("answers AS A", "A.question_id", "=", "Q.id")
                  ->leftJoin("granswers AS GR", "GR.answer_id", "=", "A.id")
                  ->select("A.id", "GR.tag")
                  ->where("quizzes.id", "=", $quiz->id)
                  ->whereNotNull("GR.id")
                  ->get();

                if (!empty($gr_answers_tags) && count($gr_answers_tags) > 0) {
                  $gr_answers_tags = collect($gr_answers_tags)->keyBy('id')->toArray();
                  $submission_answers = Submission::where('submissions.id', request("submission_code"))
                    ->leftJoin("entries", "submission_id", "=", "submissions.id")
                    ->leftJoin("answer_entry", "entry_id", "=", "entries.id")
                    ->select("answer_entry.answer_id")
                    ->whereNotNull("answer_entry.answer_id")
                    ->get()->toArray();
                  $submission_answers = array_column($submission_answers, "answer_id");
                  if (!empty($submission_answers) && count($submission_answers) > 0) {
                    foreach ($submission_answers as $answer) {
                      if (in_array($answer, array_keys($gr_answers_tags))) {
                        $tags[] = ["tagId" => $gr_answers_tags[$answer]["tag"]];
                      }
                    }
                  }
                }
                $contacts["campaign"] = ["campaignId" => $gr_integ_quiz->pivot->value];
                $contacts["tags"] = json_encode($tags);

                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://api.getresponse.com/v3/contacts',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS => json_encode($contacts),
                  CURLOPT_HTTPHEADER => array(
                    'X-Auth-Token: api-key ' . $gr_integ->key,
                  ),
                ));
                $res = curl_exec($curl);
                curl_close($curl);
              }
            } // end use of integ in quiz (quiz integ)
          } // end website integ (quiz->website_integrations)

          // ====== Convert Kit Api ======
          $ck_integ = $quiz->website_integrations('convertKit')->first(); // website CK integration
          if ($ck_integ != null && !empty($ck_integ)) {

            // quiz use of AC integration
            $ck_integ_quiz = $quiz->integration_quiz()
              ->withPivot('id', 'key', 'value')
              ->where("integration_id", $ck_integ->id)
              ->where("integrations.name", 'convertKit')
              ->first();
            if ($ck_integ_quiz != null && !empty($ck_integ_quiz)) {

              $fields = $quiz->integration_ck_get_list_fields(); // all custom and basic fields
              $values = $quiz->integration_ck;
              // dump($fields);
              // dd($values);
              // dd($fields, ["Quiz", "Question", "Result", "Field", "Date"], $values);

              if ($values != null && !empty($values) && count($values) > 0 && $fields != null && !empty($fields) && count($fields) > 0) {
                // Quiz => 0
                // Question => 1
                // Result => 2
                // Field => 3
                // Date => 4

                $types = ["Quiz", "Question", "Result", "Field", "Date"];
                $fields_types = ["First-Name" => 3, "Email" => 3, "Last-Name" => 3, "Phone" => 3];
                $contacts = [
                  "api_key" => $ck_integ->value,
                  "tags" => [],
                ];

                foreach ($fields as $field) {
                  $is_done = false;
                  foreach ($values as $value) {
                    // if ($value->type == $field->id) {
                    // dd(!$is_done, in_array($value->value_type, $types), !$is_done && in_array($value->value_type, $types), $value,$value->value_type,$types);
                    // }
                    // dd(collect($values)->pluck("type"), collect($fields)->pluck("id"));
                    if (!$is_done && in_array($value->type, ["Email", "First-Name"]) && $value->type == $field->id) {
                      if ($value->value_type == "Quiz") {
                        $name = strtolower(str_replace('-', '_', $value->type));
                        $contacts[$name] = $quiz->name;
                        $is_done = true;
                      } elseif ($value->value_type == "Question") {
                        $db_val = Question::where('questions.id', $value->value)
                          ->leftJoin("question_translations", "question_id", "=", "questions.id")
                          ->select("question_translations.title")
                          ->where("question_translations.locale", "en")
                          ->first();
                        $name = strtolower(str_replace('-', '_', $value->type));
                        $contacts[$name] = $db_val->title ?? null;
                        $is_done = true;
                      } elseif ($value->value_type == "Result") {
                        $name = strtolower(str_replace('-', '_', $value->type));
                        $contacts[$name] = "supposed value"; // integ result
                        $is_done = true;
                      } elseif ($value->value_type == "Date") {
                        $name = strtolower(str_replace('-', '_', $value->type));
                        $contacts[$name] = NOW()->format("Y-m-d");
                        $is_done = true;
                      } elseif ($value->value_type == "Field") {
                        $name = strtolower(str_replace('-', '_', $value->type));
                        $db_val = Submission::where('submissions.id', request("submission_code"))
                          ->leftJoin("entries", "submission_id", "=", "submissions.id")
                          ->leftJoin("entry_field", "entry_id", "=", "entries.id")
                          ->where("entry_field.field_id", $value->value)
                          ->first();
                        $contacts[$name] = $db_val->value ?? null;
                        $is_done = true;
                      }
                    } elseif (
                      !$is_done &&
                      $value->type == $field->id &&
                      in_array($value->value_type, array_keys($fields_types))
                    ) {
                      $db_val = Submission::where('submissions.id', request("submission_code"))
                        ->leftJoin("entries", "submission_id", "=", "submissions.id")
                        ->leftJoin("entry_field", "entry_id", "=", "entries.id")
                        ->where("entry_field.field_id", $value->value)
                        ->first();
                      $name = strtolower(str_replace('-', '_', $value->type));
                      $contacts[$name] = $db_val->value ?? null;
                      $is_done = true;
                    } elseif (!$is_done && $value->type == $field->id) {
                      if (!$is_done && in_array($value->value_type, $types)) {
                        if ($value->value_type == "Quiz") {
                          $contacts["fields"][$value->type] = $quiz->name;
                          $is_done = true;
                        } elseif ($value->value_type == "Question") {
                          $db_val = Question::where('questions.id', $value->value)
                            ->leftJoin("question_translations", "question_id", "=", "questions.id")
                            ->select("question_translations.title")
                            ->where("question_translations.locale", "en")
                            ->first();
                          $contacts["fields"][$value->type] = $db_val->title ?? null;
                          $is_done = true;
                        } elseif ($value->value_type == "Result") {
                          $contacts["fields"][$value->type] = "supposed value"; // integ result
                          $is_done = true;
                        } elseif ($value->value_type == "Date") {
                          $contacts["fields"][$value->type] = NOW()->format("Y-m-d");
                          $is_done = true;
                        }
                      } elseif (!$is_done && in_array($value->value_type, array_keys($fields_types))) {
                        $db_val = Submission::where('submissions.id', request("submission_code"))
                          ->leftJoin("entries", "submission_id", "=", "submissions.id")
                          ->leftJoin("entry_field", "entry_id", "=", "entries.id")
                          ->where("entry_field.field_id", $value->value)
                          ->first();
                        $contacts["fields"][$value->type] = $db_val->value ?? null;
                        $is_done = true;
                      }
                    }
                  }
                }

                $ck_answers_tags = Quiz::leftJoin("questions AS Q", "Q.quiz_id", "=", "quizzes.id")
                  ->leftJoin("answers AS A", "A.question_id", "=", "Q.id")
                  ->leftJoin("ckanswers AS CK", "CK.answer_id", "=", "A.id")
                  ->select("A.id", "CK.tag")
                  ->where("quizzes.id", "=", $quiz->id)
                  ->whereNotNull("CK.id")
                  ->get();

                if (!empty($ck_answers_tags) && count($ck_answers_tags) > 0) {
                  $ck_answers_tags = collect($ck_answers_tags)->keyBy('id')->toArray();

                  $submission_answers = Submission::where('submissions.id', request("submission_code"))
                    ->leftJoin("entries", "submission_id", "=", "submissions.id")
                    ->leftJoin("answer_entry", "entry_id", "=", "entries.id")
                    ->select("answer_entry.answer_id")
                    ->whereNotNull("answer_entry.answer_id")
                    ->get()->toArray();
                  $submission_answers = array_column($submission_answers, "answer_id");
                  if (!empty($submission_answers) && count($submission_answers) > 0) {
                    foreach ($submission_answers as $answer) {
                      if (in_array($answer, array_keys($ck_answers_tags))) {
                        array_push($contacts["tags"], $ck_answers_tags[$answer]["tag"]);
                      }
                    }
                  }
                }

                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://api.convertkit.com/v3/sequences/' . $ck_integ_quiz->pivot->value . '/subscribe',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS => json_encode($contacts),
                  CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json; charset=utf-8'
                  ),
                ));

                $response = curl_exec($curl);

                $contact = $response != null ? json_decode($response) : null;
              }
            } // end use of integ in quiz (quiz integ)
          } // end website integ (quiz->website_integrations)

        }
      }
    }

    return $return;
  }

  public function storeResult(Request $request) {


    $request->validate([
      "submission_code" => "required|exists:submissions,id",
      "result_id"   => "required|exists:results,id",
    ]);

    if (request("submission_code") != null && !empty(request("submission_code"))) {

      $submission = Submission::find(request("submission_code"));
      $submission->result_id = request("result_id");
      return $submission->save();
    }
  }
}
