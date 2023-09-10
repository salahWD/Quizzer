<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    public function showLoginForm() {
      if(!session()->has('url.intended')) {
        session(['url.intended' => url()->previous()]);
      }
      return view('auth.login');
    }

    protected function authenticated(Request $request, $user) {

      $user->check_subsicription();
      if (!$user->is_admin() && !$user->is_sub()) {
        return redirect()->route("pricing");
      }else {
        if (session()->has('url.intended')) {

          $link = session()->get('url.intended');
          session()->forget('url.intended');
          return redirect($link);
        }
        return redirect()->route("show_websites");
      }

    }

    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct() {
      $this->middleware('guest')->except('logout');
    }
}
