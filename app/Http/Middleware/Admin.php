<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Auth;

class Admin extends Middleware {

  public function handle($request, Closure $next, ...$guards) {

    if (Auth::check()) {
      if (!auth()->check() || !auth()->user()->is_admin()) {
        return abort(404);
      }
      return $next($request);
    }
    return redirect()->route('user.login');
  }
}
