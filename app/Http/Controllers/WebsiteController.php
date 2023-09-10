<?php

namespace App\Http\Controllers;

use File;
use App\Models\Website;
use App\Models\Quiz;
use Illuminate\Http\Request;

class WebsiteController extends Controller {

  public function __construct() {
    $this->middleware('auth');
  }

  public function index() {

    $websites = Website::where("user_id", "=", auth()->id())->get();

    return view("websites.index", ["websites" => $websites]);
  }

  public function create(Website $website) {

    if (auth()->user()->can_add_website()) {
      return view("websites.create")->with("website_id", $website->id);
    }

    return redirect()->route("show_websites")->with('status', 'you can\'t add more websites, please upgrade your plane');

  }

  public function store(Request $request) {

    if (auth()->user()->can_add_website()) {
      $validated = $request->validate([
        'url' => 'required|url|max:60',
        'company' => 'required',
      ]);

      Website::create([
        "url" => request("url"),
        "company" => request("company"),
        "user_id" => auth()->user()->id
      ]);

      return redirect()->route('show_websites');

    }else {
      return redirect()->route("show_websites")->with('status', 'you can\'t add more websites, please upgrade your plane');
    }

  }

  public function show(Website $website) {

    $quizzes = $website->get_quizzes();
    foreach ($quizzes as $quiz) {
      $quiz->setDefaultLocale(app()->getLocale());
    }

    return view("websites.show", compact("website", "quizzes"));
  }

  public function templates(Website $website) {

    $templates = Quiz::where("is_template", 1)
        ->where("status", 1)
        ->get();

    return view("templates.show_template", compact("templates", "website"));

  }

  public function config($website_id) {

    $website = Website::where("id", $website_id)->with("integrations")->get()->first();

    return view("websites.config", compact("website"));

  }

  public function update_config(Website $website, Request $request) {

    if (auth()->user()->can_custom_domain()) {
      $validation_rules['custom_domain'] = 'sometimes|nullable|regex:/^(?:https?:\/\/)?(?:www[0-9]*\.)?(.*)$/m';
      $request->validate($validation_rules);
    }

    foreach (config("integrations.INTEGRATIONS") as $integ) {
      foreach ($integ["FIELDS"] as $key => $field) {
        $website->integrations()->updateOrInsert(
          ["name" => $integ['INTEGRATION'], 'website_id' => $website->id],
          [$field['INPUT'][2] => request($integ['INTEGRATION'] . "_" . $integ['ID'] . "_" . $key)]
        );
      }
    }

    if (null !== request("custom_domain") && !empty(request("custom_domain")) && auth()->user()->can_custom_domain()) {
      $website->custom_domain = request("custom_domain");
    }

    $website->save();
    return redirect()->route("website_config", $website->id);

  }

  public function edit(Website $website) {

    return view("websites.edit", compact("website"));
  }

  public function update(Request $request, Website $website) {

    if (auth()->user()->id == $website->user->id) {
      $validated = $request->validate([
        'url' => 'required|url|max:60',
        'company' => 'required',
        'image' => "nullable|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte
      ]);

      $website->url = request("url");
      $website->company = request("company");
      $watermark = request("show_watermark") ? "on" : "off";
      $website->show_watermark = $watermark == "on" ? 1: 0;

      if ($request->hasFile('image')) {
        $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . $request->image->getClientOriginalName());
        $request->image->move(public_path('images\uploads'), $unique_name);
        $old_image = $website->logo_image;
        $website->logo_image = $unique_name;
        if ($old_image != null && File::exists(public_path("images\uploads") . $old_image)) {
          File::delete(public_path("images\uploads") . $old_image);
        }
      }

      $website->save();

    }

    return redirect()->route("show_websites");

  }

  public function destroy(Website $website) {
    if (auth()->user()->id == $website->user->id) {
      Website::where("id", "=", $website->id)->delete();
    }
    return redirect()->route("show_websites");
  }

}
