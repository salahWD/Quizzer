<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class is_subscribed {

  public function handle(Request $request, Closure $next) {

    if (auth()->user()->is_sub()) {

      return $next($request);
    }else {
      return redirect(route("pricing"));
    }

  }
}
