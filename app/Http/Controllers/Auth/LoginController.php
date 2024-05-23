<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller {

  protected function authenticated($request, $user) {

    $user->check_subsicription();
    if ($user->is_admin() || $user->is_sub()) {
      if (session()->has('url.intended')) {
        $link = session()->get('url.intended');
        session()->forget('url.intended');
        return redirect($link);
      }
      return redirect()->route("show_websites");
    }
    return redirect()->route("pricing");
  }

  use AuthenticatesUsers;

  protected $redirectTo = RouteServiceProvider::HOME;

  public function __construct() {
    $this->middleware('guest')->except('logout');
  }
}
