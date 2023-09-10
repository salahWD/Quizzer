<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Route;
use Closure;
use Illuminate\Http\Request;
use App\Models\Website;

class domainCheck {

  public function handle(Request $request, Closure $next) {

    $domain = $request->getHost();
    if (config("app.APP_DOMAIN") != $domain) {

      $query = Website::select("id")->where("custom_domain", "=", $domain)->get();

      if ($query->count() > 0) {

        // $name = Route::currentRouteName();

        // if ($name != "show_quiz") {
        //   $quiz_id = $query->first()->id;
        //   return redirect()->to(route("show_quiz", $quiz_id));
        // }
// dd(route("show_quiz",$quiz_id));
      }else{
        abort(404);
      }

    }

    return $next($request);

  }


}
