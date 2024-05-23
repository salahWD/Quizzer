<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\PayController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\CasestudyController;
use App\Models\StripePayment;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Middleware\SetAppLang;
use Illuminate\Support\Facades\DB;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Result;


Route::redirect('/', "/home");
Route::view('/test', "test");

Auth::routes();

// =================================

Route::get('/getresponse', function (Request $request) {
  dd($request);
})->name('getresponse');
Route::get('/home', function () {
  return view('home');
})->name('home');
Route::get('/Privacy-Policy', function () {
  return view('Privacy-Policy');
})->name('PrivacyPolicy');
Route::get('/TermsandConditions', function () {
  return view('TermsandConditions');
})->name('TermsandConditions');

Route::view('/features', 'features')->name("features");

Route::group(['prefix' => 'case-studies'], function () {
  Route::get('/', [CasestudyController::class, 'index'])->name("case-studies");
  Route::get('/create', [CasestudyController::class, 'create'])->name("case-studies.create");
  Route::post('/create', [CasestudyController::class, 'store'])->name("case-studies.store");
  Route::get('/{casestudy}', [CasestudyController::class, 'show'])->name("case-studies.show");
  Route::get('/{casestudy}/edit', [CasestudyController::class, 'edit'])->name("case-studies.edit");
  Route::post('/{casestudy}/update', [CasestudyController::class, 'update'])->name("case-studies.update");
  Route::get('/{casestudy}/delete', [CasestudyController::class, 'destroy'])->name("case-studies.delete");
});

Route::get('/pricing', [PlanController::class, 'index'])->name("pricing");
Route::middleware("auth")->group(function () {
  Route::get('/pricing/{plan}', [PlanController::class, 'show'])->name("offer");
  Route::post('/payment/{plan}/process', [PayController::class, "process"])->name("process_payment");
  Route::get('/payment/{plan}', [PayController::class, 'payment_info'])->name('payment_info');
});

Route::middleware(["auth", "is_admin"])->group(function () {
  Route::get('/admin/reports', [DashboardController::class, 'reports'])->name("dash_general_reports");
  Route::get('/admin/payments', [DashboardController::class, 'payments_reports'])->name("dash_payments_reports");
});

Route::get('/package/upgrade/{package}', [PayController::class, "upgrade"])->middleware('auth')->name("change_package");
Route::get('/payment/success', [PayController::class, "success"])->name("success_payment");
Route::view('/payment/cancel', "payment.cancel")->name("cancel_payment");
Route::view('/payment/pending', "payment.pending")->name("pending_payment");
Route::post('/payment/stripe-webhook', [StripePayment::class, "webhook"])->name("stripe_webhook");
Route::get('/payments', [PayController::class, "manage"])->name("manage_payments");
Route::get('/payment/{peyment_id}/approve', [PayController::class, "approve"])->name("approve_payment");
Route::get('/payment/{peyment_id}/delete', [PayController::class, "destroy"])->name("delete_payment");

// =================================

/* Websites Routes */
Route::get('/websites', [WebsiteController::class, 'index'])->name('show_websites');
Route::get('/websites/create', [WebsiteController::class, 'create'])->name('create_website');
Route::get('/websites/{website}/templates', [WebsiteController::class, 'templates'])->name('website_templates');
Route::get('/websites/{website}/config', [WebsiteController::class, 'config'])->name('website_config');
Route::get('/websites/{website}/custom_domain', [WebsiteController::class, 'custom_domain'])->name('website_custom_domain');
Route::post('/websites/{website}/custom_domain', [WebsiteController::class, 'update_custom_domain'])->name('website_update_custom_domain');
Route::get('/websites/manage_domains', [WebsiteController::class, 'manage_domains'])->name('website_manage_domains');
Route::post('/websites/{website}/manage_domains', [WebsiteController::class, 'update_manage_domains'])->name('website_update_domains_status');
Route::post('/websites/{website}/config', [WebsiteController::class, 'update_config'])->name('website_update_config');
Route::post('/websites/{website}/delete_logo', [WebsiteController::class, 'delete_logo'])->name('delete_website_logo')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::get('/websites/{website}', [WebsiteController::class, 'show'])->name('show_website');
Route::get('/websites/{website}/edit', [WebsiteController::class, 'edit'])->name('edit_website');
Route::post('/websites', [WebsiteController::class, 'store'])->name('store_website');
Route::post('/websites/{website}', [WebsiteController::class, 'update'])->name('update_website');
Route::post('/websites/{website}/delete', [WebsiteController::class, 'destroy'])->name('delete_website');

// =================================

/* Users Routes */
Route::get('users/search', [UserController::class, 'search'])->name('users_search');
Route::get('users/create', [UserController::class, 'create'])->name('create_user');
Route::middleware("auth")->group(function () {
  Route::get("users/me", [UserController::class, "show"])->name('profile');
  Route::get('/users/{user}', [UserController::class, 'user_info'])->name('user_info');
  Route::post("users/me", [UserController::class, "update_profile"])->name('update_profile');
  Route::get('/users/me/subscription', [UserController::class, 'subscription'])->name('subscription_info');
  Route::get('/users/me/subscription/cancel', [UserController::class, 'subscription_cancel'])->name('subscription_cancel');
});
Route::get('users', [UserController::class, 'index'])->name('show_users');
// Route::get('users/{user}', [UserController::class, 'show'])->name('show_user');
Route::post('/users', [UserController::class, 'store'])->name('store_user');
Route::post('/users/{user}', [UserController::class, 'update'])->name('update_user');
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('edit_user');
Route::post('/users/{user}/delete', [UserController::class, 'destroy'])->name('delete_user');

// =================================

/* Coupon Routes */
Route::get('/coupons', [CouponController::class, 'index'])->name("show_coupons");
Route::view('/coupon/create', "coupons.create", ["packages" => config("pricing.PRICING_PACKAGES")])->name("create_coupon");
Route::get('/coupon/{coupon}', [CouponController::class, 'show'])->name("coupon_info");
Route::get('/coupon/{coupon}/edit', [CouponController::class, 'edit'])->name("edit_coupon");
Route::get('/coupon/{coupon}/delete', [CouponController::class, 'destroy'])->name("delete_coupon");
Route::post('ajax/coupon/check', [CouponController::class, 'check'])->name("check_coupon");
Route::post('/coupon', [CouponController::class, 'store'])->name("store_coupon");
Route::post('/coupon/{coupon}', [CouponController::class, 'update'])->name("update_coupon");

// =================================

/* questions Routes */
// Route::post('/question/{quiz}', [QuizController::class, 'update'])->name("update_quiz");

// =================================

/* Quiz Routes */
// Route::get('/test', [PayController::class, 'test'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
// Route::post('/test', function (Request $request) {
//   dd($request->all());
// })->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/websites/{website}/quizzes', [QuizController::class, 'index'])->name('quizzes_base');
Route::get('/websites/{website}/quizzes/create', [QuizController::class, 'create'])->name("create_quiz");
Route::get('/tq/{quiz}', [QuizController::class, 'show'])->name("show_quiz");
Route::get('/ajax/add-view/{question}', [Question::class, 'add_view']);
Route::get('/ajax/add-view-result/{result}', [Result::class, 'add_view']);
Route::post('ajax/tq/{quiz}', [AnswerController::class, 'answering'])->name('answering');
Route::post('ajax/store-result', [AnswerController::class, 'storeResult']);
Route::post('ajax/tq/form/{quiz}', [AnswerController::class, 'formEntry'])->name("form_entry");
Route::get('/quizzes/{quiz}/build', [QuizController::class, 'build'])->name("build_quiz");
Route::get('/quizzes/copy/{website}/{quiz}', [QuizController::class, 'copy'])->name("copy_quiz");
Route::get('/quizzes/{quiz}/design', [QuizController::class, 'design'])->name("design_quiz");
Route::get('/quizzes/{quiz}/config', [QuizController::class, 'config'])->name("config_quiz");
Route::get('/quizzes/{quiz}/share', [QuizController::class, 'share'])->name("sharing_quiz");
Route::get('/quizzes/{quiz}/report', [QuizController::class, 'report'])->name("report_quiz");
Route::get('/admin/quizzes', [DashboardController::class, 'quizzes_reports'])->name("dash_quizzes_reports");
Route::get('/quizzes/{quiz}/translate', [QuizController::class, 'translate'])->name("translate_quiz");
Route::get('/export/{quiz}/submissions', [QuizController::class, 'export_submissions'])->name("export_submissions_data");
Route::post('/quizzes/{quiz}/design', [QuizController::class, 'update_design'])->name("update_design_quiz");
Route::post('/quizzes/{quiz}/delete_image', [QuizController::class, 'delete_image'])->name("delete_design_image");
Route::post('/quizzes/{quiz}/delete_intro_image', [QuizController::class, 'delete_intro_image'])->name("delete_intro_image");
Route::post('/quizzes/{quiz}/config', [QuizController::class, 'update_config'])->name("update_config_quiz");
Route::post('/quizzes/{quiz}/translate', [QuizController::class, 'update_translate'])->name("update_translate_quiz");
Route::post('/quizzes/{quiz}/publish', [QuizController::class, 'update_status'])->name("publish_quiz");
Route::post('/websites/{website}/quizzes', [QuizController::class, 'store'])->name("store_quiz");
Route::post('/quizzes/{quiz}', [QuizController::class, 'update'])->name("update_quiz");
Route::get('/quizzes/{quiz}/delete', [QuizController::class, 'destroy'])->name("delete_quiz");
Route::post('/quizzes/{quiz}/popup-images', [QuizController::class, 'popup_images'])->name("share_popup_images");

Route::get('/ajax/quiz/{quiz}/ac/{list}/fields', [IntegrationController::class, 'ac_fields'])->name("ac_field");
Route::post('/quiz/{quiz}/integrations/fb', [IntegrationController::class, 'update_fb'])->name("update_integrations_fb");
Route::post('/quiz/{quiz}/integrations/tk', [IntegrationController::class, 'update_tk'])->name("update_integrations_tk");
Route::post('/quiz/{quiz}/integrations/sc', [IntegrationController::class, 'update_sc'])->name("update_integrations_sc");
Route::post('/quiz/{quiz}/integrations/ac', [IntegrationController::class, 'update_ac'])->name("update_integrations_ac");
Route::post('/quiz/{quiz}/integrations/ck', [IntegrationController::class, 'update_ck'])->name("update_integrations_ck");
Route::post('/quiz/{quiz}/integrations/gr', [IntegrationController::class, 'update_gr'])->name("update_integrations_gr");
Route::post('/quiz/{quiz}/integrations/ga', [IntegrationController::class, 'update_ga'])->name("update_integrations_ga");
Route::post('/quiz/{quiz}/integrations/tm', [IntegrationController::class, 'update_tm'])->name("update_integrations_tm");
Route::post('/quiz/{quiz}/integrations/tw', [IntegrationController::class, 'update_tw'])->name("update_integrations_tw");
Route::post('/quiz/{quiz}/integrations/fb/delete', [IntegrationController::class, 'delete_fb'])->name("delete_integrations_fb");
Route::post('/quiz/{quiz}/integrations/tk/delete', [IntegrationController::class, 'delete_tk'])->name("delete_integrations_tk");
Route::post('/quiz/{quiz}/integrations/sc/delete', [IntegrationController::class, 'delete_sc'])->name("delete_integrations_sc");
Route::post('/quiz/{quiz}/integrations/ac/delete', [IntegrationController::class, 'delete_ac'])->name("delete_integrations_ac");
Route::post('/quiz/{quiz}/integrations/ck/delete', [IntegrationController::class, 'delete_ck'])->name("delete_integrations_ck");
Route::post('/quiz/{quiz}/integrations/gr/delete', [IntegrationController::class, 'delete_gr'])->name("delete_integrations_gr");
Route::post('/quiz/{quiz}/integrations/ga/delete', [IntegrationController::class, 'delete_ga'])->name("delete_integrations_ga");
Route::post('/quiz/{quiz}/integrations/tm/delete', [IntegrationController::class, 'delete_tm'])->name("delete_integrations_tm");
Route::post('/quiz/{quiz}/integrations/tw/delete', [IntegrationController::class, 'delete_tw'])->name("delete_integrations_tw");

// =================================

/* Themes Routes */
Route::get('/themes/create', [ThemeController::class, 'create'])->name("create_theme");
Route::post('/themes/', [ThemeController::class, 'store'])->name("store_theme");
Route::get('/themes/{theme}/', [ThemeController::class, 'edit'])->name("edit_theme");
Route::post('/themes/{theme}/', [ThemeController::class, 'update'])->name("update_theme");
Route::post('/themes/{theme}/delete-image', [ThemeController::class, 'delete_image'])->name("delete_image");
Route::post('/themes/{theme}/delete', [ThemeController::class, 'destroy'])->name("delete_theme");
Route::post('/quizzes/{quiz}/themes', [ThemeController::class, 'copy'])->name("select_theme_quiz");

// =================================

/* Template Routes */
Route::get('/templates/', [TemplateController::class, 'show'])->name("show_template");
Route::get('/templates/create', [TemplateController::class, 'create'])->name("create_template");
Route::post('/templates/', [TemplateController::class, 'store'])->name("store_template");
Route::get('/templates/{quiz}/delete', [TemplateController::class, 'destroy'])->name("delete_template");
Route::get('/templates/info/manage', [TemplateController::class, 'manage_info'])->name("info_manage");
Route::get('/templates/{quiz}', [TemplateController::class, 'edit'])->name("edit_template");
Route::post('/templates/{quiz}', [TemplateController::class, 'update'])->name("update_template");
Route::get('/templates/{quiz}/theme', [TemplateController::class, 'theme'])->name("template_theme");
Route::post('/templates/{quiz}/theme', [TemplateController::class, 'update_theme'])->name("update_template_theme");
Route::post('/templates/{quiz}/select_theme', [TemplateController::class, 'select_theme'])->name("select_template_theme");
Route::get('/preview/{quiz}', [TemplateController::class, 'preview'])->name("preview_template");
Route::post('/website/{website}/copy/{quiz_id}', [TemplateController::class, 'copy'])->name("select_template");

// =================================

/* Ajax Routes */
Route::group(['prefix' => 'ajax', 'middleware' => 'auth'], function () {
  Route::post('/quiz/{quiz}/name', function (Quiz $quiz) {

    if (auth()->user()->is_admin() || auth()->user()->id == $quiz->website->user->id) {
      $validated = request()->validate([
        'name' => 'required|max:255'
      ]);

      $quiz->translateOrNew(app()->getLocale())->name = request("name");
      $quiz->save();
      return "Quiz Title Updated Successfully";
    } else {
      return "Update Failed !!";
    }
  })->name("edit_quiz_name");
  Route::post('/quizzes/{quiz}/add_question', [QuestionController::class, 'store'])->name("add_question");
  Route::post('/quizzes/{quiz}/reorder', [QuestionController::class, 'reorder'])->name("reorder_quiz_questions");
  Route::post('/questions/{question}/delete', [QuestionController::class, 'destroy'])->name('delete_question');
  Route::post('/questions/{question}/copy', [QuestionController::class, 'copy'])->name('copy_question');
  Route::post('/question/{question}/reorder', [AnswerController::class, 'reorder'])->name("reorder_question_answers");
  Route::get('/question/{question}/mapped', [QuestionController::class, 'mapped']);
  Route::post('/question/{question}/mapping', [QuestionController::class, 'mapping'])->name("mapping_question");
  Route::get('/question/{question}/conditioned', [QuestionController::class, 'conditioned']);
  Route::post('/question/{question}/conditioning', [QuestionController::class, 'conditioning']);
  Route::get('/question/{question}', [QuestionController::class, 'show'])->name('show_question');
  Route::post('/question/{question}', [QuestionController::class, 'update'])->name('update_question');
  Route::post('/question/{question}/image_actions', [QuestionController::class, 'image_actions']);
  Route::post('/quizzes/{quiz}/add_result', [ResultController::class, 'store'])->name("add_result");
  Route::get('/results/{result}', [ResultController::class, 'show']);
  Route::post('/results/{result}', [ResultController::class, 'update']);
  Route::post('/results/{result}/delete', [ResultController::class, 'destroy']);
  Route::post('/results/{result}/copy', [ResultController::class, 'copy']);
});

Route::group(['prefix' => 'ajax'], function () {
  Route::get("/submission/{submission}", [QuizController::class, 'get_submission']);
});
// =================================

Route::get('logout', [LoginController::class, 'logout'])->name('logout');
