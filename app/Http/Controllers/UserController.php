<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Auth\RegisterController;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller {

  public function __construct() {
    $this->middleware('is_admin');
  }

  public function index(Request $request) {
    $users = User::paginate(20);
    if ($users->currentPage() > $users->lastPage()) {
      return abort(404);
    }
    return view("users.manage", compact("users"));
  }

  public function show(User $user, Request $request) {

  }

  public function create(Request $request) {

    $packages = config("pricing.PRICING_PACKAGES");
    return view("users.create", compact("packages"));
  }

  public function edit(User $user, Request $request) {
    $packages = config("pricing.PRICING_PACKAGES");
    return view("users.create", compact("user", "packages"));// edit
  }

  public function store(Request $request) {

    if ($request->get("name") == null) {
      $request->request->add(['name' => 'new user']);
    }

    $request->validate([
      "name" => "required|string|max:255",
      "email" => "required|string|email|max:255|unique:users",
      "subscription_end" => "sometimes|nullable|date|after:yesterday",
      "status" => "required|numeric|in:" . implode(",", User::Available_Packages),
      "password" => "required|string|min:4",
    ]);

    User::create([
      'name' => request('name'),
      'email' => request('email'),
      'subscription_end' => request('subscription_end'),
      'status' => request('status'),
      'password' => Hash::make(request('password')),
    ]);

    return redirect()->route("show_users");
  }

  public function update(User $user, Request $request) {

    $request->validate([
      "name" => "required|string|max:255",
      "email" => "sometimes|string|email|max:255|unique:users",
      "subscription_end" => "sometimes|nullable|date|after:yesterday",
      "status" => "required|numeric|in:" . implode(",", User::Available_Packages),
      "password" => "sometimes|string|min:4",
    ]);

    $user->name = request('name');
    if (request("email") != null) {
      $user->email = request('email');
    }
    $user->subscription_end = request('subscription_end');
    $user->status = User::Available_Levels[request('status')];
    if (request("password") != null) {
      $user->password = Hash::make(request('password'));
    }

    $user->save();
    return redirect()->route("show_users");

  }

  public function destroy(User $user, Request $request) {
    $user->delete();
    return redirect()->route("show_users");
  }

}
