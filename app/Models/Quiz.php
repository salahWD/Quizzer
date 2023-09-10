<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;
use App\Models\Submission;
use App\Models\Website;
use App\Models\Template;
use App\Models\QuizTranslation;
use App\Models\Integration;
use App\Models\Ac;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Quiz extends Model implements TranslatableContract {
  use HasFactory;
  use Translatable;

  public const LOGICS = [
    "scoring" => 1,
    "outcome" => 2
  ];

  public const STATUS = [
    "unpublished" => 0,
    "published" => 1,
  ];

  public $translatedAttributes = [
    'policy_label',
    'intro_title',
    'intro_description',
    'template_desc',
    'name',
    'intro_btn',
  ];

  protected $fillable = ["type", "is_template", "website_id"];

  /*=== Relations ===*/

  public function template() {
    return $this->hasOne(Template::class);
  }

  public function website() {
    return $this->belongsTo(Website::class);
  }

  public function website_with_integrations() {
    return $this->belongsTo(Website::class)->with("integrations");
  }

  /*
    --- @Goal => get website integration name + current quiz info of the integration
    --- @name => the name of the integration on the website like [facebook, activeCompaign, ...etc]
  */
  public function integrations($name = null) {
    $integrations = $this->integration_quiz()->withPivot('id', 'key', 'value');
    if ($name != null) {
      $integrations->where("integrations.name", $name);
      return $integrations->first();
    }
    return $integrations->get();
  }

  public function website_integrations($name = null) {
    if ($name != null) {
      return $this->belongsTo(Website::class, 'website_id')
          ->leftJoin('integrations', 'integrations.website_id', '=', 'websites.id')
          ->where('integrations.name', $name)
          ->select('websites.id', 'integrations.*');
        }else {
      return $this->belongsTo(Website::class, 'website_id')
          ->leftJoin('integrations', 'integrations.website_id', '=', 'websites.id')
          ->select('websites.id', 'integrations.*');
    }
  }

  public function integration_quiz() {
    return $this->belongsToMany(Integration::class, 'integration_quiz');
  }

  public function integration_ac() {
    return $this->hasMany(Ac::class);
  }

  public function integration_ac_get_lists() {
    $integ = $this->website->get_integration('activeCompaign');

    if ($integ != null) {

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => $integ->url . '/api/3/lists',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          'Api-Token: ' . $integ->key,
        ),
      ));

      $response = curl_exec($curl);
      curl_close($curl);
      return json_decode($response);

    }
    return false;
  }

  public function integration_ac_get_list_fields($list_id) {
    $integ = $this->website->get_integration('activeCompaign');

    if ($integ != null) {

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => $integ->url . '/api/3/fields',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          'Api-Token: ' . $integ->key,
        ),
      ));

      $response = curl_exec($curl);
      curl_close($curl);
      $fields = json_decode($response);
      $fields_ids = array_column(array_filter($fields->fieldRels, function ($field) use ($list_id) {
        if ($field->relid == $list_id) {
          return true;
        }
      }), 'field');

      if (count($fields->fields) > 0) {
        $custom_fields = array_filter($fields->fields, function ($field) use ($fields_ids) {
          if (in_array($field->id, $fields_ids)) {
            return true;
          }
        });

        return [
          (Object) [
            'id' => "Email",
            "type" => 'email',
            "title" => 'Email',
          ],
          (Object) [
            'id' => "First-Name",
            "type" => 'text',
            "title" => 'First Name',
          ],
          (Object) [
            'id' => "Last-Name",
            "type" => 'text',
            "title" => 'Last Name',
          ],
          (Object) [
            'id' => "Phone",
            "type" => 'text',
            "title" => 'Phone',
          ],
          ...$custom_fields
        ];

      }

    }
    return false;
  }

  public function integration_ac_get_field_options($is_email=false) {
    if ($is_email) {
      $ac_values = DB::select("
        SELECT F.id AS ac_value_id, FT.label AS ac_value_title, 3 AS ac_value_type
          FROM fields AS F
          INNER JOIN field_translations AS FT ON FT.field_id = F.id
          INNER JOIN questions AS Q ON Q.id = F.question_id
        WHERE F.type = 3 AND Q.type = 3 AND Q.quiz_id = $this->id AND FT.locale = 'en' AND FT.label IS NOT NULL AND FT.label != ''
      ");
      return $ac_values;
    }else {

      $ac_values = DB::select("
        SELECT *
        FROM (SELECT QU.id AS ac_value_id, QUT.name AS ac_value_title, 0 AS ac_value_type
          FROM quizzes AS QU
          INNER JOIN quiz_translations AS QUT ON QUT.quiz_id = QU.id
          WHERE QU.id = $this->id AND QUT.locale = 'en' AND QUT.name IS NOT NULL AND QUT.name != ''
        UNION
          SELECT Q.id AS ac_value_id, QT.title AS ac_value_title, 1 AS ac_value_type
          FROM questions AS Q
          INNER JOIN question_translations AS QT ON QT.question_id = Q.id
          WHERE Q.quiz_id = $this->id AND QT.locale = 'en' AND Q.type IN (1, 2) AND QT.title IS NOT NULL AND QT.title != ''
        UNION
          SELECT F.id AS ac_value_id, FT.label AS ac_value_title, 3 AS ac_value_type
          FROM fields AS F
          INNER JOIN field_translations AS FT ON FT.field_id = F.id
          INNER JOIN questions AS Q ON Q.id = F.question_id
        WHERE Q.quiz_id = $this->id AND FT.locale = 'en' AND FT.label IS NOT NULL AND FT.label != '') x
      ");

      array_push($ac_values, (Object) [
        'ac_value_id' => 'result',
        'ac_value_title' => 'result',
        'ac_value_type' => 2
      ]);

      array_push($ac_values, (Object) [
        'ac_value_id' => 'date',
        'ac_value_title' => 'date',
        'ac_value_type' => 4
      ]);

      return $ac_values;

    }
    return false;
  }

  // public function integration_ac_get_field_values() {
  //   $ac_values = DB::select(
  //     "SELECT *
  //       FROM (SELECT QU.id AS ac_value_id, QUT.name AS ac_value_title, 0 AS ac_value_type
  //         FROM quizzes AS QU
  //         INNER JOIN quiz_translations AS QUT ON QUT.quiz_id = QU.id
  //         WHERE QU.id = $this->id AND QUT.locale = 'en' AND QUT.name IS NOT NULL AND QUT.name != ''
  //       UNION
  //         SELECT Q.id AS ac_value_id, QT.title AS ac_value_title, 1 AS ac_value_type
  //         FROM questions AS Q
  //         INNER JOIN question_translations AS QT ON QT.question_id = Q.id
  //         WHERE Q.quiz_id = $this->id AND QT.locale = 'en' AND Q.type IN (1, 2) AND QT.title IS NOT NULL AND QT.title != ''
  //       UNION
  //         SELECT F.id AS ac_value_id, FT.label AS ac_value_title, 3 AS ac_value_type
  //         FROM fields AS F
  //         INNER JOIN field_translations AS FT ON FT.field_id = F.id
  //         INNER JOIN questions AS Q ON Q.id = F.question_id
  //       WHERE Q.quiz_id = $this->id AND FT.locale = 'en' AND FT.label IS NOT NULL AND FT.label != '') x"
  //   );

  //   array_push($ac_values, (Object) [
  //     'ac_value_id' => 'result',
  //     'ac_value_title' => 'result',
  //     'ac_value_type' => 2
  //   ]);

  //   array_push($ac_values, (Object) [
  //     'ac_value_id' => 'date',
  //     'ac_value_title' => 'date',
  //     'ac_value_type' => 4
  //   ]);

  //   return $ac_values ?? false;

  // }

  public function owner() {
    return User::join("websites", "websites.user_id", "=", "users.id")
        ->join("quizzes", "quizzes.website_id", "=", "websites.id")
        ->select("users.*")
        ->where("quizzes.id", $this->id)->get()->first();
  }

  public function questions() {
    return $this->hasMany(Question::class);
  }

  public function questions_with_answers() {
    return $this->hasMany(Question::class)->with("answers");
  }

  public function results() {
    return $this->hasMany(Result::class);
  }

  public function submissions() {
    return $this->hasMany(Submission::class);
  }

  public function submissions_leads() {
    return $this->hasMany(Submission::class)
        ->select("submissions.*", "EF.value")
        ->leftJoin("questions AS Q", "Q.quiz_id", "=", DB::raw("submissions.quiz_id AND Q.type = 3"))
        ->leftJoin("fields AS F", "F.question_id", "=", DB::raw("Q.id AND F.is_lead_email = 1 AND F.type = 3"))// type id of email
        ->leftJoin("entries AS E", "E.submission_id", "=", "submissions.id")
        ->leftJoin("entry_field AS EF", "EF.field_id", "=", DB::raw("F.id AND EF.entry_id = E.id"))
        ->groupBy("EF.id")
        ->orderBy("submissions.created_at", "desc")->toSql();
  }

  public function submissions_is_done() {
    return $this->hasMany(Submission::class)
        ->where("is_done", 1);
  }

  public function submissions_count() {
    return $this->hasMany(Submission::class)->count();
  }

  public function submissions_is_done_count() {
    return $this->hasMany(Submission::class)
        ->where("is_done", 1)
        ->count();
  }

  /*=== Action Methods ===*/

  public function can_open_quiz() {

    $user = $this->owner();

    if ($user->status == "admin") {return true;}
    if ($user->status == "inactive") {return false;}

    $total_responses = DB::table("submissions AS S")
    ->selectRaw("COUNT(S.id) AS responses_count")
    ->join("quizzes AS Q", "Q.id", "=", "S.quiz_id")
    ->join("websites AS W", "W.id", "=", "Q.website_id")
    ->join("users AS U", "U.id", "=", "W.user_id")
    ->where("U.id", $user->id)
    // ->where("U.id", DB::raw("(select users.id FROM users INNER JOIN websites ON websites.user_id = users.id INNER JOIN quizzes ON quizzes.website_id = websites.id WHERE quizzes.id = " . $this->id . ")"))
    ->get()->first()->responses_count;

    $packages = config("pricing.PRICING_PACKAGES");
    return $total_responses < $packages[User::Levels_Packages[$user->status]]["RESPONSES"];
  }

  public function add_view() {
    $this->views += 1;
    $this->save();
  }

  /*=== Custom Joins Methods ===*/

  public function translated_questions() {
    return $this->hasMany(Question::class)->with("translations");
  }

  public function ordered_question() {
    return $this->hasMany(Question::class)->orderBy("order");
  }

  public function ar_questions() {
    return $this->hasMany(Question::class)
        ->join('question_translations', 'question_translations.question_id', '=', 'questions.id')
        ->select('questions.*', 'question_translations.locale AS locale', 'question_translations.title AS ar_title', 'question_translations.description AS ar_description')
        ->where('locale', 'ar')
        ->orderBy('order')->get();
  }

  public function en_questions() {
    return $this->hasMany(Question::class)
        ->join('question_translations', 'question_translations.question_id', '=', 'questions.id')
        ->select('questions.*', 'question_translations.locale AS locale', 'question_translations.title AS en_title', 'question_translations.description AS en_description')
        ->where('locale', 'en')
        ->orderBy('order')->get();
  }

  public function langs_questions() {
    return $this->hasMany(Question::class)
        ->join('question_translations AS en', 'en.question_id', '=', DB::Raw('questions.id AND en.locale = "en"'))
        ->leftJoin('question_translations AS ar', 'ar.question_id', '=', DB::Raw('questions.id AND ar.locale = "ar"'))
        ->select('questions.*', 'en.title AS en_title', 'en.description AS en_description', 'en.button_label AS en_button_label', 'ar.title AS ar_title', 'ar.description AS ar_description', 'ar.button_label AS ar_button_label')
        ->orderBy('order')->get();
  }

  /*========== Results Methods ==========*/


  public function translated_results() {
    return $this->hasMany(Result::class)->with("translations");
  }

  public function ar_results() {
    return $this->hasMany(Result::class)
        ->join('result_translations AS ar', 'ar.result_id', '=', DB::Raw('results.id AND ar.locale = "ar"'))
        ->select('results.*', 'ar.title AS ar_title', 'ar.description AS ar_description', 'ar.button_label AS ar_button_label')
        ->get();
  }

  public function en_results() {
    return $this->hasMany(Result::class)
        ->join('result_translations AS en', 'en.result_id', '=', DB::Raw('results.id AND en.locale = "en"'))
        ->select('results.*', 'en.title AS en_title', 'en.description AS en_description', 'en.button_label AS en_button_label')
        ->get();
  }

  public function langs_results() {
    return $this->hasMany(Result::class)
        ->join('result_translations AS en', 'en.result_id', '=', DB::Raw('results.id AND en.locale = "en"'))
        ->leftJoin('result_translations AS ar', 'ar.result_id', '=', DB::Raw('results.id AND ar.locale = "ar"'))
        ->select('results.*', 'en.title AS en_title', 'en.description AS en_description', 'en.button_label AS en_button_label', 'en.score_message AS en_score_message', 'ar.title AS ar_title', 'ar.description AS ar_description', 'ar.button_label AS ar_button_label', 'ar.score_message AS ar_score_message')
        ->get();
  }

  public function ar_score_results() {
    return $this->hasMany(Result::class)
        ->join('result_translations AS ar', 'ar.result_id', '=', DB::Raw('results.id AND ar.locale = "ar"'))
        ->select('results.*', 'ar.title AS ar_title', 'ar.description AS ar_description', 'ar.button_label AS ar_button_label', 'ar.score_message AS ar_score_message')
        ->whereNotNull('min_score')
        ->whereNotNull('max_score')
        ->get();
  }

  public function en_score_results() {
    return $this->hasMany(Result::class)
        ->join('result_translations AS en', 'en.result_id', '=', DB::Raw('results.id AND en.locale = "en"'))
        ->select('results.*', 'en.title AS en_title', 'en.description AS en_description', 'en.button_label AS en_button_label', 'en.score_message AS en_score_message')
        ->whereNotNull('min_score')
        ->whereNotNull('max_score')
        ->get();
  }

  public function langs_score_results() {
    return $this->hasMany(Result::class)
        ->join('result_translations AS en', 'en.result_id', '=', DB::Raw('results.id AND en.locale = "en"'))
        ->leftJoin('result_translations AS ar', 'ar.result_id', '=', DB::Raw('results.id AND ar.locale = "ar"'))
        ->select('results.*', 'en.title AS en_title', 'en.description AS en_description', 'en.button_label AS en_button_label', 'en.score_message AS en_score_message', 'ar.title AS ar_title', 'ar.description AS ar_description', 'ar.button_label AS ar_button_label', 'ar.score_message AS ar_score_message')
        ->whereNotNull('min_score')
        ->whereNotNull('max_score')
        ->get();
  }

  public function ar_outcome_results() {
    return $this->hasMany(Result::class)
        ->join('result_translations AS ar', 'ar.result_id', '=', DB::Raw('results.id AND ar.locale = "ar"'))
        ->select('results.*', 'ar.title AS ar_title', 'ar.description AS ar_description', 'ar.button_label AS ar_button_label')
        ->get();
  }

  public function en_outcome_results() {
    return $this->hasMany(Result::class)
        ->join('result_translations AS en', 'en.result_id', '=', DB::Raw('results.id AND en.locale = "en"'))
        ->select('results.*', 'en.title AS en_title', 'en.description AS en_description', 'en.button_label AS en_button_label')
        ->get();
  }

  public function langs_outcome_results() {
    return $this->hasMany(Result::class)
        ->join('result_translations AS en', 'en.result_id', '=', DB::Raw('results.id AND en.locale = "en"'))
        ->leftJoin('result_translations AS ar', 'ar.result_id', '=', DB::Raw('results.id AND ar.locale = "ar"'))
        ->select('results.*', 'en.title AS en_title', 'en.description AS en_description', 'en.button_label AS en_button_label', 'ar.title AS ar_title', 'ar.description AS ar_description', 'ar.button_label AS ar_button_label')
        ->get();
  }

  public static function get_type_id($type) {
    return static::LOGICS[$type] ?? null;
  }

  public static function get_type($type_id) {
    return array_search($type_id, static::LOGICS) ?? null;
  }

  public function type() {
    return static::get_type($this->type);
  }

  public static function get_status_id($status) {
    return static::STATUS[$status] ?? null;
  }

  public static function get_status($status_id) {
    return array_search($status_id, static::STATUS) ?? null;
  }

  public function status() {
    return static::get_status($this->status);
  }

  public function themes() {
    return [
      [
        'id' => 1,
        'name' => 'Nature Theme',
        'background_color' => '#73c6b6',
        'font_family' => 'Arial, sans-serif',
        'background_image_url' => 'https://example.com/nature-theme.jpg',
        'text_color' => '#333333',
        'btn_color' => '#4caf50',
        'btn_text_color' => '#ffffff',
        'border_color' => '#999999',
        'highlight_color' => '#99cc66',
        'answer_bg_color' => '#f2f2f2',
        'answer_text_color' => '#666666',
        'result_btn_color' => '#2e87b4',
        'result_btn_text_color' => '#ffffff',
      ],
      [
        'id' => 2,
        'name' => 'Streets Theme',
        'background_color' => '#999999',
        'font_family' => 'Helvetica, sans-serif',
        'background_image_url' => 'https://example.com/streets-theme.jpg',
        'text_color' => '#ffffff',
        'btn_color' => '#ff9800',
        'btn_text_color' => '#ffffff',
        'border_color' => '#444444',
        'highlight_color' => '#f2f2f2',
        'answer_bg_color' => '#333333',
        'answer_text_color' => '#f2f2f2',
        'result_btn_color' => '#1f79a1',
        'result_btn_text_color' => '#ffffff',
      ],
      [
        'id' => 3,
        'name' => 'Tech Theme',
        'background_color' => '#2e87b4',
        'font_family' => 'Roboto, sans-serif',
        'background_image_url' => 'https://example.com/tech-theme.jpg',
        'text_color' => '#ffffff',
        'btn_color' => '#007bff',
        'btn_text_color' => '#ffffff',
        'border_color' => '#dddddd',
        'highlight_color' => '#f2f2f2',
        'answer_bg_color' => '#f8f9fa',
        'answer_text_color' => '#333333',
        'result_btn_color' => '#28a745',
        'result_btn_text_color' => '#ffffff',
      ],
    ];
  }

}
