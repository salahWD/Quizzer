<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Result;
use App\Models\Ac;
use App\Models\Ck;
use App\Models\Gr;
use App\Models\Answer;
use App\Models\Acanswer;
use App\Models\Ckanswer;
use App\Models\Granswer;
use App\Models\Quizgoogle;
use App\Models\Itemgoogle;
use App\Models\Fbitem;
use App\Models\Tkanswer;
use App\Models\Tkitem;
use App\Models\Scanswer;
use App\Models\Scitem;
use App\Models\Fbanswer;
use App\Models\Twitem;
use App\Models\Twanswer;

class IntegrationController extends Controller {


  public function update_fb(Quiz $quiz, Request $request) {

    $request->validate([
      "fb" => "sometimes|array",
      "fb.fb-custom-event" => "nullable|sometimes|string",
      "fb.questions" => "nullable|sometimes|array",// questions culd be any item (question or result)
      "fb.questions.*.id" => "required|integer",
      "fb.questions.*.val" => "sometimes|nullable|string",
      "fb.questions.*.answers" => "sometimes|nullable|array",
      "fb.questions.*.answers.*.id" => "required|integer|exists:answers,id",
      "fb.questions.*.answers.*.val" => "sometimes|nullable|string",
    ]);

    $fb_integ = $quiz->website_integrations('facebook')->get();

    if ($fb_integ != null && count($fb_integ) > 0) {

      if ($quiz->integration_quiz()->where('integration_id', $fb_integ->first()->id)->count() > 0) {
        $fb = $quiz->integration_quiz()->updateExistingPivot(
          $fb_integ->first()->id,
          ["value" => request("fb.fb-custom-event") ?? null]
        );
      }else {
        $quiz->integration_quiz()->attach([
          $fb_integ->first()->id => ['value' => request("fb.fb-custom-event") ?? null],
        ]);
      }

      $quiz_items = $quiz->all_quiz_items();

      if ($quiz_items != null && count($quiz_items) > 0 && request("fb.questions") != null && count(request("fb.questions")) > 0) {
        foreach($quiz_items as $item) {
          $finish = false;
          foreach(request("fb.questions") as $request_item) {
            if (!$finish) {
              if ($request_item["id"] == $item->id) {
                Fbitem::updateOrInsert(
                  ["item_id" => $item->id, "item_type" => $item::item_type],
                  ["event" => $request_item["val"]]
                );

                if(isset($request_item["answers"]) && $request_item["answers"] != null && count($request_item["answers"]) > 0 && $item::item_type == 1 && $item->answers != null && count($item->answers) > 0) {
                  foreach($item->answers as $answer) {
                    $answer_finish = false;
                    foreach($request_item["answers"] as $request_answer) {
                      if (!$answer_finish) {
                        if ($request_answer["id"] == $answer->id) {
                          if ($request_answer["val"] != null) {
                            Fbanswer::updateOrInsert(
                              ["answer_id" => $answer->id],
                              ["event" => $request_answer["val"]]
                            );
                          }else {
                            Fbanswer::where("answer_id", $answer->id)->delete();
                          }
                          $answer_finish = true;
                        }
                      }
                    }
                    if (!$answer_finish) {
                      Fbanswer::where("answer_id", $answer->id)->delete();
                    }
                  }

                }
                $finish = true;
              }
            }
          }
          if (!$finish) {
            Fbitem::where("item_id", $item->id)->where("item_type", $item::item_type)->delete();
          }
        }
      }
      // dd("Done",request()->all());
    }

    return redirect()->route('config_quiz', $quiz->id);

  }

  public function update_tk(Quiz $quiz, Request $request) {

    $request->validate([
      "tk" => "sometimes|array",
      "tk.tk-custom-event" => "nullable|sometimes|string",
      "tk.questions" => "nullable|sometimes|array",// questions culd be any item (question or result)
      "tk.questions.*.id" => "required|integer",
      "tk.questions.*.val" => "sometimes|nullable|string",
      "tk.questions.*.answers" => "sometimes|nullable|array",
      "tk.questions.*.answers.*.id" => "required|integer|exists:answers,id",
      "tk.questions.*.answers.*.val" => "sometimes|nullable|string",
    ]);

    $tk_integ = $quiz->website_integrations('tiktok')->get();

    if ($tk_integ != null && count($tk_integ) > 0) {

      if ($quiz->integration_quiz()->where('integration_id', $tk_integ->first()->id)->count() > 0) {
        $tk = $quiz->integration_quiz()->updateExistingPivot(
          $tk_integ->first()->id,
          ["value" => request("tk.tk-custom-event") ?? null]
        );
      }else {
        $quiz->integration_quiz()->attach([
          $tk_integ->first()->id => ['value' => request("tk.tk-custom-event") ?? null],
        ]);
      }

      $quiz_items = $quiz->all_quiz_items();

      if ($quiz_items != null && count($quiz_items) > 0 && request("tk.questions") != null && count(request("tk.questions")) > 0) {
        foreach($quiz_items as $item) {
          $finish = false;
          foreach(request("tk.questions") as $request_item) {
            if (!$finish) {
              if ($request_item["id"] == $item->id) {

                if ($item::item_type == 1) {// question 1
                  $item = Question::find($item->id);
                }else {
                  $item = Result::find($item->id);
                }

                if (isset($item)) {
                  $item->tk_event()->updateOrCreate(
                    ["item_id" => $item->id, "item_type" => ["App\\Models\\Question", "App\\Models\\Result"][$item::item_type - 1]],
                    ["event" => $request_item["val"]]
                  );
                }

                if(isset($request_item["answers"]) && $request_item["answers"] != null && count($request_item["answers"]) > 0 && $item::item_type == 1 && $item->answers != null && count($item->answers) > 0) {
                  foreach($item->answers as $answer) {
                    $answer_finish = false;
                    foreach($request_item["answers"] as $request_answer) {
                      if (!$answer_finish) {
                        if ($request_answer["id"] == $answer->id) {
                          if ($request_answer["val"] != null) {
                            Tkanswer::updateOrInsert(
                              ["answer_id" => $answer->id],
                              ["event" => $request_answer["val"]]
                            );
                          }else {
                            Tkanswer::where("answer_id", $answer->id)->delete();
                          }
                          $answer_finish = true;
                        }
                      }
                    }
                    if (!$answer_finish) {
                      Tkanswer::where("answer_id", $answer->id)->delete();
                    }
                  }

                }
                $finish = true;
              }
            }
          }
          if (!$finish) {
            Tkitem::where("item_id", $item->id)->where("item_type", $item::item_type)->delete();
          }
        }
      }
      // dd("Done",request()->all());
    }

    return redirect()->route('config_quiz', $quiz->id);

  }

  public function update_tw(Quiz $quiz, Request $request) {

    $request->validate([
      "tw" => "sometimes|array",
      "tw.tw-custom-event" => "nullable|sometimes|string",
      "tw.questions" => "nullable|sometimes|array",// questions culd be any item (question or result)
      "tw.questions.*.id" => "required|integer",
      "tw.questions.*.val" => "sometimes|nullable|string",
      "tw.questions.*.answers" => "sometimes|nullable|array",
      "tw.questions.*.answers.*.id" => "required|integer|exists:answers,id",
      "tw.questions.*.answers.*.val" => "sometimes|nullable|string",
    ]);

    $tw_integ = $quiz->website_integrations('twitter')->get();

    if ($tw_integ != null && count($tw_integ) > 0) {

      if ($quiz->integration_quiz()->where('integration_id', $tw_integ->first()->id)->count() > 0) {
        $tw = $quiz->integration_quiz()->updateExistingPivot(
          $tw_integ->first()->id,
          ["value" => request("tw.tw-custom-event") ?? null]
        );
      }else {
        $quiz->integration_quiz()->attach([
          $tw_integ->first()->id => ['value' => request("tw.tw-custom-event") ?? null],
        ]);
      }

      $quiz_items = $quiz->all_quiz_items();

      if ($quiz_items != null && count($quiz_items) > 0 && request("tw.questions") != null && count(request("tw.questions")) > 0) {
        foreach($quiz_items as $item) {
          $finish = false;
          foreach(request("tw.questions") as $request_item) {
            if (!$finish) {
              if ($request_item["id"] == $item->id) {

                if ($item::item_type == 1) {// question 1
                  $item = Question::find($item->id);
                }else {
                  $item = Result::find($item->id);
                }

                if (isset($item)) {
                  $item->tw_event()->updateOrCreate(
                    ["item_id" => $item->id, "item_type" => ["App\\Models\\Question", "App\\Models\\Result"][$item::item_type - 1]],
                    ["event" => $request_item["val"]]
                  );
                }

                if(isset($request_item["answers"]) && $request_item["answers"] != null && count($request_item["answers"]) > 0 && $item::item_type == 1 && $item->answers != null && count($item->answers) > 0) {
                  foreach($item->answers as $answer) {
                    $answer_finish = false;
                    foreach($request_item["answers"] as $request_answer) {
                      if (!$answer_finish) {
                        if ($request_answer["id"] == $answer->id) {
                          if ($request_answer["val"] != null) {
                            Twanswer::updateOrInsert(
                              ["answer_id" => $answer->id],
                              ["event" => $request_answer["val"]]
                            );
                          }else {
                            Twanswer::where("answer_id", $answer->id)->delete();
                          }
                          $answer_finish = true;
                        }
                      }
                    }
                    if (!$answer_finish) {
                      Twanswer::where("answer_id", $answer->id)->delete();
                    }
                  }

                }
                $finish = true;
              }
            }
          }
          if (!$finish) {
            Twitem::where("item_id", $item->id)->where("item_type", $item::item_type)->delete();
          }
        }
      }
      // dd("Done",request()->all());
    }

    return redirect()->route('config_quiz', $quiz->id);

  }

  public function update_sc(Quiz $quiz, Request $request) {

    $request->validate([
      "sc_on_off" => "sometimes|nullable|accepted",
    ]);

    $sc_integ = $quiz->website_integrations('snapchat')->first();

    if ($sc_integ != null && $sc_integ->count() > 0) {

      $quiz_integ = $quiz->integrations("snapchat");
      if ($quiz_integ != null) {
        if (request("sc_on_off") != null && !empty(request("sc_on_off"))) {
          $quiz->integration_quiz()->updateExistingPivot(
            $quiz_integ->id,
            ["value" => 1]
          );
        }else {
          DB::table("integration_quiz")->where("id", $quiz_integ->pivot->id)->delete();
        }
      }else {
        if (request("sc_on_off") != null && !empty(request("sc_on_off"))) {
          $quiz->integration_quiz()->attach([
            $sc_integ->id => ['value' => 1],
          ]);
        }else {
          DB::table("integration_quiz")->where("integration_id", $sc_integ->id)->where("quiz_id", $quiz->id)->delete();
        }
      }

      return redirect()->route('config_quiz', $quiz->id);
    }

    return abort(404);

  }

  public function update_ac(Quiz $quiz, Request $request) {

    $request->validate([
      "ac" => "sometimes|array",
      "ac.list" => "required|nullable|integer",
      "ac.fields" => "required|array|min:4",
      "ac.fields.*.id" => "required|string",
      "ac.fields.*.type" => "sometimes|nullable|string|in:Quiz,Question,Result,Field,Date",
      "ac.fields.*.val" => "sometimes|nullable|string",
      "ac.tags" => "sometimes|array",
      "ac.tags.*.answer" => "sometimes|nullable|integer|exists:answers,id",
      "ac.tags.*.tag" => "sometimes|nullable|integer",
    ], [
      "ac.list" => "wrong Active Campaign list is selected",
      "ac.fields" => "active campaign fields are required",
    ]);

    $ac_integ = $quiz->website_integrations('activeCompaign')->get();

    if ($ac_integ != null && count($ac_integ) > 0) {

      $integratino_quiz = $quiz->integration_quiz()
          ->withPivot('id', 'key', 'value')
          ->where("integration_id", $ac_integ->first()->id)
          ->where("integrations.name", 'activeCompaign')
          ->first();

      if ($integratino_quiz != null) {
        if ($integratino_quiz->count() > 0) {
          $quiz->integration_quiz()->updateExistingPivot(
            $integratino_quiz->id,
            ["value" => request("ac.list") ?? null]
          );
        }
      }else {
        $quiz->integration_quiz()->attach([
          $ac_integ->first()->id => ['value' => request("ac.list") ?? null],
        ]);
      }

      if (null !== request('ac.fields') && count(request('ac.fields')) > 0) {

        $active_list = $quiz->integrations('activeCompaign');

        if (!empty($active_list) && isset($active_list->pivot) && isset($active_list->pivot->value)) {

          $fields = $quiz->integration_ac_get_list_fields($active_list->pivot->value);// custom and standard fields of AC

          if (count($fields) > 0 && count(request('ac.fields')) > 0) {
            foreach(request('ac.fields') as $request_field) {
              $this_field_is_done = false;
              if ($request_field["val"] != null && !empty($request_field["val"]) && $request_field["type"] != null && !empty($request_field["type"])) {
                foreach($fields as $field) {
                  if (!$this_field_is_done) {
                    if ($request_field['id'] == $field->id) {
                      Ac::updateOrInsert(
                        [
                          'quiz_id' => $quiz->id,
                          'type' => $request_field['id'],
                        ],
                        [
                          'value_type' => $request_field['type'],
                          'value' => $request_field['val']
                        ]
                      );
                      $this_field_is_done = true;
                    }
                  }
                }
              }
              if($this_field_is_done == false) {
                Ac::where('quiz_id', $quiz->id)
                  ->where('type', $request_field['id'])
                  ->delete();
              }
            }
          }
        }

      }

    }else {
      abort();
    }

    if (request("ac.tags") != null && count(request("ac.tags")) > 0) {
      $available_answers = array_column($quiz->questions()->leftJoin("answers", "answers.question_id", "=", "questions.id")->select("answers.id")->whereNotNull("answers.id")->get()->toArray(), "id");
      foreach (request("ac.tags") as $request_tag) {
        if (in_array($request_tag["answer"], $available_answers)) {
          if ($request_tag["tag"] != null) {
            Acanswer::updateOrCreate(
              [
                'answer_id' => $request_tag["answer"],
              ],
              [
                'tag' => $request_tag["tag"],
              ]
            );
          }else {
            Acanswer::where('answer_id', $request_tag["answer"])->delete();
          }
        }
      }
    }

    return redirect()->route('config_quiz', $quiz->id);

  }

  public function update_ck(Quiz $quiz, Request $request) {

    $request->validate([
      "ck" => "sometimes|array",
      "ck.list" => "required|nullable|integer",
      "ck.fields" => "required|array|min:2",
      "ck.fields.*.id" => "required|string",
      "ck.fields.*.type" => "sometimes|nullable|string|in:Quiz,Question,Result,Field,Date",
      "ck.fields.*.val" => "sometimes|nullable|string",
      "ck.tags" => "sometimes|array",
      "ck.tags.*.answer" => "sometimes|nullable|integer|exists:answers,id",
      "ck.tags.*.tag" => "sometimes|nullable|integer",
    ], [
      "ck.list" => "wrong ConvertKit list is selected",
      "ck.fields" => "ConvertKit fields are required",
    ]);

    $integ = $quiz->website_integrations('convertKit')->get();

    if ($integ != null && count($integ) > 0) {

      $quiz_integration = $quiz->integration_quiz()
          ->withPivot('id', 'key', 'value')
          ->where("integration_id", $integ->first()->id)
          ->where("integrations.name", 'convertKit')
          ->first();

      if ($quiz_integration != null) {
        if ($quiz_integration->count() > 0) {
          $quiz->integration_quiz()->updateExistingPivot(
            $quiz_integration->id,
            ["value" => request("ck.list") ?? null]
          );
        }
      }else {
        $quiz->integration_quiz()->attach([
          $integ->first()->id => ['value' => request("ck.list") ?? null],
        ]);
      }

      if (null !== request('ck.fields') && count(request('ck.fields')) > 0) {

        $active_list = $quiz->integrations('convertKit');

        if (!empty($active_list) && isset($active_list->pivot) && isset($active_list->pivot->value)) {

          $fields = $quiz->integration_ck_get_list_fields($active_list->pivot->value);// custom and standard fields of CK

          if (count($fields) > 0 && count(request('ck.fields')) > 0) {
            foreach(request('ck.fields') as $request_field) {
              $this_field_is_done = false;
              if ($request_field["val"] != null && !empty($request_field["val"]) && $request_field["type"] != null && !empty($request_field["type"])) {
                foreach($fields as $field) {
                  if (!$this_field_is_done) {
                    if ($request_field['id'] == $field->id) {
                      Ck::updateOrInsert(
                        [
                          'quiz_id' => $quiz->id,
                          'type' => $request_field['id'],
                        ],
                        [
                          'value_type' => $request_field['type'],
                          'value' => $request_field['val']
                        ]
                      );
                      $this_field_is_done = true;
                    }
                  }
                }
              }
              if($this_field_is_done == false) {
                Ck::where('quiz_id', $quiz->id)
                  ->where('type', $request_field['id'])
                  ->delete();
              }
            }
          }
        }

      }

    }else {
      abort();
    }

    if (request("ck.tags") != null && count(request("ck.tags")) > 0) {
      $available_answers = array_column($quiz->questions()->leftJoin("answers", "answers.question_id", "=", "questions.id")->select("answers.id")->whereNotNull("answers.id")->get()->toArray(), "id");
      foreach (request("ck.tags") as $request_tag) {
        if (in_array($request_tag["answer"], $available_answers)) {
          if ($request_tag["tag"] != null) {
            Ckanswer::updateOrCreate(
              [
                'answer_id' => $request_tag["answer"],
              ],
              [
                'tag' => $request_tag["tag"],
              ]
            );
          }else {
            Ckanswer::where('answer_id', $request_tag["answer"])->delete();
          }
        }
      }
    }

    return redirect()->route('config_quiz', $quiz->id);

  }

  public function update_gr(Quiz $quiz, Request $request) {

    $request->validate([
      "gr" => "sometimes|array",// activeCampaign
      "gr.list" => "required|nullable|string",
      "gr.fields" => "required|array|min:2",
      "gr.fields.*.id" => "required|string",
      "gr.fields.*.type" => "sometimes|nullable|string|in:Quiz,Question,Result,Field,Date",
      "gr.fields.*.val" => "sometimes|nullable|string",
      "gr.tags" => "sometimes|array",// activeCampaign
      "gr.tags.*.answer" => "sometimes|nullable|integer|exists:answers,id",
      "gr.tags.*.tag" => "sometimes|nullable|string",
    ], [
      "gr.list" => "wrong getResponse list is selected",
      "gr.fields" => "getResponse fields are required",
    ]);

    $gr_integ = $quiz->website_integrations('getResponse')->get();

    if ($gr_integ != null && count($gr_integ) > 0) {

      $integratino_quiz = $quiz->integration_quiz()
          ->withPivot('id', 'key', 'value')
          ->where("integration_id", $gr_integ->first()->id)
          ->where("integrations.name", 'getResponse')
          ->first();

      if ($integratino_quiz != null) {
        if ($integratino_quiz->count() > 0) {
          $quiz->integration_quiz()->updateExistingPivot(
            $integratino_quiz->id,
            ["value" => request("gr.list") ?? null]
          );
        }
      }else {
        $quiz->integration_quiz()->attach([
          $gr_integ->first()->id => ['value' => request("gr.list") ?? null],
        ]);
      }

      if (null !== request('gr.fields') && count(request('gr.fields')) > 0) {

        $fields = $quiz->integration_gr_get_list_fields();
        if (count($fields) > 0 && count(request('gr.fields')) > 0) {
          foreach(request('gr.fields') as $request_field) {
            $this_field_is_done = false;
            if ($request_field["val"] != null && !empty($request_field["val"]) && $request_field["type"] != null && !empty($request_field["type"])) {
              foreach($fields as $field) {
                if (!$this_field_is_done) {
                  if ($request_field['id'] == $field->customFieldId) {
                    Gr::updateOrCreate(
                      [
                        'quiz_id' => $quiz->id,
                        'type' => $request_field['id'],
                      ],
                      [
                        'value_type' => $request_field['type'],
                        'value' => $request_field['val']
                      ]
                    );
                    $this_field_is_done = true;
                  }
                }
              }
            }
            if($this_field_is_done == false) {
              Gr::where('quiz_id', $quiz->id)
                ->where('type', $request_field['id'])
                ->delete();
            }

          }
        }

      }

    }else {
      abort();
    }

    if (request("gr.tags") != null && count(request("gr.tags")) > 0) {
      $available_answers = array_column($quiz->questions()->leftJoin("answers", "answers.question_id", "=", "questions.id")->select("answers.id")->whereNotNull("answers.id")->get()->toArray(), "id");
      foreach (request("gr.tags") as $request_tag) {
        if (in_array($request_tag["answer"], $available_answers)) {
          if ($request_tag["tag"] != null) {
            Granswer::updateOrCreate(
              [
                'answer_id' => $request_tag["answer"],
              ],
              [
                'tag' => $request_tag["tag"],
              ]
            );
          }else {
            Granswer::where('answer_id', $request_tag["answer"])->delete();
          }
        }
      }
    }

    return redirect()->route('config_quiz', $quiz->id);

  }

  public function update_ga(Quiz $quiz, Request $request) {

    $request->validate([
      "ga_start_remarket" => "present|nullable|string",
      "ga_finish_conversion" => "present|nullable|string",
      "ga_finish_remarket" => "present|nullable|string",
      "ga_items" => "required|array",
      "ga_items.*.item_id" => "present|nullable|integer",
      "ga_items.*.item_type" => "present|nullable|integer",
      "ga_items.*.remarket" => "present|nullable|string",
      "ga_items.*.conversion" => "present|nullable|string",
    ], [
      "ga_start_remarket" => "wrong quiz start remarketing tag, try again please.",
      "ga_finish_conversion" => "wrong quiz finish remarketing tag, try again please.",
      "ga_finish_remarket" => "wrong quiz finish conversion tag, try again please.",
      "ga_items.*.item_id" => "wrong item information, try again please.",
      "ga_items.*.item_type" => "wrong item information, try again please.",
      "ga_items.*.remarket" => "wrong item remarketing tag, try again please.",
      "ga_items.*.conversion" => "wrong item conversion tag, try again please.",
    ]);

    $ga_integ = $quiz->website_integrations('googleAds')->get();

    if ($ga_integ != null && count($ga_integ) > 0) {

      $items_ids = $quiz->all_items_ids();
      $tags = $quiz->google_tags;

      if ($tags != null) {
        $tags->start_marketing = str_replace(['<script>', '</script>'], '', request("ga_start_remarket"));
        $tags->finish_marketing = str_replace(['<script>', '</script>'], '', request("ga_finish_remarket"));
        $tags->finish_conversion = str_replace(['<script>', '</script>'], '', request("ga_finish_conversion"));
        $tags->save();
      }else {
        $quiz->google_tags()->create([
          "start_marketing" => str_replace(['<script>', '</script>'], '', request("ga_start_remarket")),
          "finish_marketing" => str_replace(['<script>', '</script>'], '', request("ga_finish_remarket")),
          "finish_conversion" => str_replace(['<script>', '</script>'], '', request("ga_finish_conversion")),
        ]);
      }

      if (!empty(request("ga_items")) && count(request("ga_items")) > 0) {
        foreach(request("ga_items") as $item) {
          if (in_array($item["item_id"], $items_ids)) {
            Itemgoogle::updateOrCreate(
              [
                "item_id" => $item["item_id"],
                "item_type" => $item["item_type"],
              ],
              [
                "marketing_tag" => str_replace(['<script>', '</script>'], '', $item["remarket"]),
                "conversion_tag" => str_replace(['<script>', '</script>'], '', $item["conversion"]),
            ]);
          }else {
            Itemgoogle::create([
              "item_type" => $item["item_type"],
              "marketing_tag" => str_replace(['<script>', '</script>'], '', $item["remarket"]),
              "conversion_tag" => str_replace(['<script>', '</script>'], '', $item["conversion"]),
            ]);
          }
        }
      }

    }else {
      abort();
    }

    return redirect()->route('config_quiz', $quiz->id);

  }

  public function update_tm(Quiz $quiz, Request $request) {

    $request->validate([
      "tm_on_off" => "sometimes|nullable|accepted",
    ]);

    $tm_integ = $quiz->website_integrations('tagManager')->first();

    if ($tm_integ != null && $tm_integ->count() > 0) {

      $quiz_integ = $quiz->integrations("tagManager");
      if ($quiz_integ != null) {
        if (request("tm_on_off") != null && !empty(request("tm_on_off"))) {
          $quiz->integration_quiz()->updateExistingPivot(
            $quiz_integ->id,
            ["value" => 1]
          );
        }else {
          DB::table("integration_quiz")->where("id", $quiz_integ->pivot->id)->delete();
        }
      }else {
        if (request("tm_on_off") != null && !empty(request("tm_on_off"))) {
          $quiz->integration_quiz()->attach([
            $tm_integ->id => ['value' => 1],
          ]);
        }else {
          DB::table("integration_quiz")->where("integration_id", $tm_integ->id)->where("quiz_id", $quiz->id)->delete();
        }
      }

    }else {
      abort();
    }

    return redirect()->route('config_quiz', $quiz->id);

  }

  public function ac_fields(Quiz $quiz, $list_id) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {

      return [$quiz->integration_ac_get_list_fields($list_id), collect($quiz->integration_ac_get_field_options())->keyBy('ac_value_id'), collect($quiz->integration_ac_get_field_options(true))->keyBy('ac_value_id')];
    }else {
      return false;
    }

  }

  public function ck_fields(Quiz $quiz, $list_id) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {

      return [$quiz->integration_ck_get_list_fields($list_id), collect($quiz->integration_ck_get_field_options())->keyBy('ck_value_id')];
    }else {
      return false;
    }

  }

  public function delete_fb(Quiz $quiz) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {

      $integ = $quiz->website_integrations("facebook")->first();

      if ($integ != null) {
        $quiz->integration_quiz()->wherepivot("integration_id", $integ->id)->detach();
        foreach($quiz->fb_items() as $item) {
          $item->fb_event()->delete();
          $answers = $quiz->answers;
          if ($answers != null && $answers->count() > 0) {
            foreach($answers as $answer) {
              $answer->fb_event()->delete();
            }
          }
        }
      }

      return redirect()->route("config_quiz", $quiz->id);

    }

    return abort(404);

  }

  public function delete_tk(Quiz $quiz) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {

      $integ = $quiz->website_integrations("tiktok")->first();

      if ($integ != null) {
        $quiz->integration_quiz()->wherePivot("integration_id", $integ->id)->detach();
        foreach($quiz->tk_items() as $item) {
          $item->tk_event()->delete();
          $answers = $quiz->answers;
          if ($answers != null && $answers->count() > 0) {
            foreach($answers as $answer) {
              $answer->tk_event()->delete();
            }
          }
        }
      }

      return redirect()->route("config_quiz", $quiz->id);

    }

    return abort(404);

  }

  public function delete_tw(Quiz $quiz) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {

      $integ = $quiz->website_integrations("twitter")->first();

      if ($integ != null) {
        $quiz->integration_quiz()->wherePivot("integration_id", $integ->id)->detach();
        foreach($quiz->tw_items() as $item) {
          $item->tw_event()->delete();
          $answers = $quiz->answers;
          if ($answers != null && $answers->count() > 0) {
            foreach($answers as $answer) {
              $answer->tw_event()->delete();
            }
          }
        }
      }

      return redirect()->route("config_quiz", $quiz->id);

    }

    return abort(404);

  }

  public function delete_sc(Quiz $quiz) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {

      $integ = $quiz->website_integrations("snapchat")->first();

      if ($integ != null) {
        $quiz->integration_quiz()->wherepivot("integration_id", $integ->id)->detach();
        foreach($quiz->sc_items() as $item) {
          $item->sc_event()->delete();
          $answers = $quiz->answers;
          if ($answers != null && $answers->count() > 0) {
            foreach($answers as $answer) {
              $answer->sc_event()->delete();
            }
          }
        }
      }

      return redirect()->route("config_quiz", $quiz->id);

    }

    return abort(404);

  }

  public function delete_ac(Quiz $quiz) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {

      $integ = $quiz->website_integrations("activeCompaign")->first();

      if ($integ != null) {
        $quiz->integration_quiz()->wherepivot("integration_id", $integ->id)->detach();
        $quiz->integration_ac()->delete();
        $answers = $quiz->answers;
        if ($answers != null && $answers->count() > 0) {
          foreach($answers as $answer) {
            $answer->acanswer()->delete();
          }
        }
      }

      return redirect()->route("config_quiz", $quiz->id);

    }

    return abort(404);

  }

  public function delete_ck(Quiz $quiz) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {

      $integ = $quiz->website_integrations("convertKit")->first();

      if ($integ != null) {
        $quiz->integration_quiz()->wherepivot("integration_id", $integ->id)->detach();
        $quiz->integration_ck()->delete();
        $answers = $quiz->answers;
        if ($answers != null && $answers->count() > 0) {
          foreach($answers as $answer) {
            $answer->ckanswer()->delete();
          }
        }
      }

      return redirect()->route("config_quiz", $quiz->id);

    }

    return abort(404);

  }

  public function delete_gr(Quiz $quiz) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {

      $integ = $quiz->website_integrations("getResponse")->first();

      if ($integ != null) {
        $quiz->integration_quiz()->wherepivot("integration_id", $integ->id)->detach();
        $quiz->integration_gr()->delete();
        $answers = $quiz->answers;
        if ($answers != null && $answers->count() > 0) {
          foreach($answers as $answer) {
            $answer->granswer()->delete();
          }
        }
      }

      return redirect()->route("config_quiz", $quiz->id);

    }

    return abort(404);

  }

  public function delete_ga(Quiz $quiz) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {

      $integ = $quiz->website_integrations("googleAds")->first();

      if ($integ != null) {
        $quiz->integration_quiz()->wherepivot("integration_id", $integ->id)->detach();
        $quiz->google_tags()->delete();
        $items_ids = array_column($quiz->itemgoogles(), "id");
        Itemgoogle::whereIn('id', $items_ids)->delete();
      }

      return redirect()->route("config_quiz", $quiz->id);

    }

    return abort(404);

  }

  public function delete_tm(Quiz $quiz) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {

      $integ = $quiz->website_integrations("tagManager")->first();

      if ($integ != null) {
        $quiz->integration_quiz()->wherepivot("integration_id", $integ->id)->detach();
      }

      return redirect()->route("config_quiz", $quiz->id);

    }

    return abort(404);

  }

}
