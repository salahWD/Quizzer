<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Ac;

class IntegrationController extends Controller {

  public function update(Quiz $quiz, Request $request) {

    $request->validate([
      "fb" => "sometimes|array",// facebook
      "fb.fb-custom-event" => "nullable|sometimes|string",
      "fb.questions" => "nullable|sometimes|array",
      "fb.questions.*.id" => "required|exists:questions,id",
      "fb.questions.*.val" => "sometimes|nullable|string",
      "ac" => "sometimes|array",// activeCampaign
      "ac.list" => "required|nullable|integer",
      "ac.fields" => "required|array|min:4",
      "ac.fields.*.id" => "required|string",
      "ac.fields.*.type" => "sometimes|nullable|string|in:Quiz,Question,Result,Field,Date",
      "ac.fields.*.val" => "sometimes|nullable|string",
    ], [
      "ac.list" => "wrong list is selected",
      "ac.fields" => "active campaign fields are required",
    ]);

    $integs = $quiz->website_integrations()->get();
    $fb_integs = $integs;

    if ($fb_integs->filter(fn($item) => $item->name == 'facebook')->count() > 0) {

      $fb_integ = $fb_integs->filter(fn($item) => $item->name == 'facebook');

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

      // add facebook integs to questions on this quiz

    }

    $ac_integs = $integs;// all website integrations

    if ($ac_integs->filter(fn($item) => $item->name == 'activeCompaign')->count() > 0) {// website integ is ok

      $ac_integ = $ac_integs->filter(fn($item) => $item->name == 'activeCompaign');// website integ
      // $ac_integ = $quiz->integration_quiz()->where("integrations.name", 'activeCompaign')->get();// quiz integ

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
                      Ac::updateOrCreate(
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

            }
          }
        }

      }


    }else {
      dd(' no active compaign');
    }

    // return dd(request()->all());
    return redirect()->route('config_quiz', $quiz->id);

  }

  public function ac_fields(Quiz $quiz, $list_id) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user_id) {

      return [$quiz->integration_ac_get_list_fields($list_id), collect($quiz->integration_ac_get_field_options())->keyBy('ac_value_id'), collect($quiz->integration_ac_get_field_options(true))->keyBy('ac_value_id')];
    }else {
      return false;
    }

  }

}
