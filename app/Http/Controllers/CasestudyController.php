<?php

namespace App\Http\Controllers;

use App\Models\Casestudy;
use Illuminate\Http\Request;
use App\Models\Quiz;
use File;

class CasestudyController extends Controller {

  public function __construct() {
    $this->middleware('is_admin')->except("show", "index");
  }

  public function index() {
    $cases = CaseStudy::all();
    $best_cases = CaseStudy::where("is_best", 1)->get();
    return view('case-studies.index', compact('cases', 'best_cases'));
  }

  public function create() {
    $quizzes = Quiz::all();
    return view("case-studies.create", compact("quizzes"));
  }

  public function store(Request $request) {

    $request->validate([
      "name" => "required|string",
      "person" => "required|string",
      "quiz" => "nullable|exists:quizzes,id",
      "is_best" => "sometimes|accepted",
      "job" => "required|string",
      "desc" => "required|string",
      "image" => "required|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte
      "video" => "required|url",
      "story-title" => "required|string",
      "story" => "required|string",
      "goal-title" => "required|string",
      "goal" => "required|string",
      "solution" => "required|string",
      "keys-1-title" => "required|string",
      "keys-1-desc" => "required|string",
      "keys-1-img" => "required|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte
      "keys-2-title" => "nullable|string",
      "keys-2-desc" => "nullable|string",
      "keys-2-img" => "nullable|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte
      "keys-3-title" => "nullable|string",
      "keys-3-img" => "nullable|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte
      "keys-3-desc" => "nullable|string",
      "result-title" => "required|string",
      "result-desc" => "required|string",
    ]);

    $image = $request->file("image");
    $image_name = str_replace(" ", "", date('mdYHis') . uniqid() . $image->getClientOriginalName());
    $image->move(public_path('images/uploads'), $image_name);

    $key_1_img = $request->file("keys-1-img");
    $key_1_img_name = str_replace(" ", "", date('mdYHis') . uniqid() . $key_1_img->getClientOriginalName());
    $key_1_img->move(public_path('images/uploads'), $key_1_img_name);

    if ($request->hasFile("keys-2-img")) {
      $key_2_img = $request->file("keys-2-img");
      $key_2_img_name = str_replace(" ", "", date('mdYHis') . uniqid() . $key_2_img->getClientOriginalName());
      $key_2_img->move(public_path('images/uploads'), $key_2_img_name);
    }

    if ($request->hasFile("keys-3-img")) {
      $key_3_img = $request->file("keys-3-img");
      $key_3_img_name = str_replace(" ", "", date('mdYHis') . uniqid() . $key_3_img->getClientOriginalName());
      $key_3_img->move(public_path('images/uploads'), $key_3_img_name);
    }

    $casestudy_info = [
      "name"         => request("name"),
      "person"       => request("person"),
      "quiz_id"      => request("quiz"),
      "is_best"      => request("is_best") == "on" ? 1 : 0,
      "job_title"    => request("job"),
      "desc"         => request("desc"),
      "image"        => $image_name ?? null,
      "video"        => request("video"),
      "story_title"  => request("story-title"),
      "story"        => request("story"),
      "goal_title"   => request("goal-title"),
      "goal"         => request("goal"),
      "solution"     => request("solution"),
      "key_1_title"  => request("keys-1-title"),
      "key_1_img"    => $key_1_img_name ?? null,
      "key_1_desc"   => request("keys-1-desc"),
      "key_2_title"  => request("keys-2-title"),
      "key_2_img"    => $key_2_img_name ?? null,
      "key_2_desc"   => request("keys-2-desc"),
      "key_3_title"  => request("keys-3-title"),
      "key_3_img"    => $key_3_img_name ?? null,
      "key_3_desc"   => request("keys-3-desc"),
      "result_title" => request("result-title"),
      "result_desc"  => request("result-desc"),
    ];

    Casestudy::create($casestudy_info);

    return redirect()->route("case-studies");

  }

  public function show(Casestudy $casestudy) {
    $more_cases = CaseStudy::whereNotIn('id', [$casestudy->id])->inRandomOrder()->take(3)->get();
    return view("case-studies.show", compact('casestudy', 'more_cases'));
  }

  public function edit(Casestudy $casestudy) {
    $quizzes = Quiz::all();
    return view("case-studies.create", compact("quizzes", "casestudy"));
  }

  public function update(Request $request, Casestudy $casestudy) {

    $request->validate([
      "name" => "required|string",
      "person" => "required|string",
      "job" => "required|string",
      "quiz" => "nullable|exists:quizzes,id",
      "is_best" => "sometimes|accepted",
      "desc" => "required|string",
      "image" => "nullable|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte
      "video" => "required|url",
      "story-title" => "required|string",
      "story" => "required|string",
      "goal-title" => "required|string",
      "goal" => "required|string",
      "solution" => "required|string",
      "keys-1-title" => "required|string",
      "keys-1-desc" => "required|string",
      "keys-1-img" => "nullable|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte
      "keys-2-title" => "nullable|string",
      "keys-2-desc" => "nullable|string",
      "keys-2-img" => "nullable|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte
      "keys-3-title" => "nullable|string",
      "keys-3-img" => "nullable|image|mimes:png,jpg,jpeg|max:2048",// max = 2 mega byte
      "keys-3-desc" => "nullable|string",
      "result-title" => "required|string",
      "result-desc" => "required|string",
    ]);

    if (request('image') != null && $request->hasFile("image")) {
      if ($casestudy->image != NULL && File::exists(public_path("images/uploads/$casestudy->image"))) {
        File::delete(public_path("images/uploads/$casestudy->image"));
      }
      $image = $request->file("image");
      $image_name = str_replace(" ", "", date('mdYHis') . uniqid() . $image->getClientOriginalName());
      $image->move(public_path('images/uploads'), $image_name);
      $casestudy->image = $image_name;
    }

    if (request('keys-1-img') != null && $request->hasFile("keys-1-img")) {
      if ($casestudy->key_1_img != NULL && File::exists(public_path("images/uploads/$casestudy->key_1_img"))) {
        File::delete(public_path("images/uploads/$casestudy->key_1_img"));
      }
      $key_1_img = $request->file("keys-1-img");
      $key_1_img_name = str_replace(" ", "", date('mdYHis') . uniqid() . $key_1_img->getClientOriginalName());
      $key_1_img->move(public_path('images/uploads'), $key_1_img_name);
      $casestudy->key_1_img = $key_1_img_name;
    }

    if (request('keys-2-img') != null && $request->hasFile("keys-2-img")) {
      if ($casestudy->key_2_img != NULL && File::exists(public_path("images/uploads/$casestudy->key_2_img"))) {
        File::delete(public_path("images/uploads/$casestudy->key_2_img"));
      }
      $img = $request->file("keys-2-img");
      $img_name = str_replace(" ", "", date('mdYHis') . uniqid() . $img->getClientOriginalName());
      $img->move(public_path('images/uploads'), $img_name);
      $casestudy->key_2_img = $img_name;
    }

    if ($request->hasFile("keys-3-img")) {
      if ($casestudy->key_3_img != NULL && File::exists(public_path("images/uploads/$casestudy->key_3_img"))) {
        File::delete(public_path("images/uploads/$casestudy->key_3_img"));
      }
      $img = $request->file("keys-3-img");
      $img_name = str_replace(" ", "", date('mdYHis') . uniqid() . $img->getClientOriginalName());
      $img->move(public_path('images/uploads'), $img_name);
      $casestudy->key_3_img = $img_name;
    }

    $casestudy->name         = request("name");
    $casestudy->person       = request("person");
    $casestudy->quiz_id      = request("quiz") ?? null;
    $casestudy->is_best      = request("is_best") == "on" ? 1 : 0;
    $casestudy->job_title    = request("job");
    $casestudy->desc         = request("desc");
    $casestudy->video        = request("video");
    $casestudy->story_title  = request("story-title");
    $casestudy->story        = request("story");
    $casestudy->goal         = request("goal");
    $casestudy->goal_title   = request("goal-title");
    $casestudy->solution     = request("solution");
    $casestudy->key_1_title  = request("keys-1-title");
    $casestudy->key_1_desc   = request("keys-1-desc");
    $casestudy->key_2_title  = request("keys-2-title");
    $casestudy->key_2_desc   = request("keys-2-desc");
    $casestudy->key_3_title  = request("keys-3-title");
    $casestudy->key_3_desc   = request("keys-3-desc");
    $casestudy->result_title = request("result-title");
    $casestudy->result_desc  = request("result-desc");

    $casestudy->save();

    return redirect()->route("case-studies");

  }

  public function destroy(Casestudy $casestudy) {
    $casestudy->delete();
    return redirect()->route("case-studies");
  }

}
