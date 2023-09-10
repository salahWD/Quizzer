<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller {


  public function reports() {

    $users = DB::table("users")->count();
    $websites = DB::table("websites")->count();
    $quizzes = DB::table("quizzes")->count();
    $submissions = DB::table("submissions")->count();
    $stats = [];

    $stats['inactive_payments'] = DB::table("payments")->where("status", "pending")->count();
    $stats['active_payments'] = DB::table("payments")->where("status", "completed")->count();

    $paid_users = DB::table("payments")
        ->select(DB::raw('COUNT(payments.id) as paymentsCount'))
        ->groupBy('user_id')
        ->where("status", "completed")
        ->get();

    $stats['repaid_users'] = count($paid_users->filter(function ($paid) {
      return $paid->paymentsCount > 1;
    }));

    $stats['paid_users'] = count($paid_users->filter(function ($paid) {
      return $paid->paymentsCount <= 1;
    }));

    return view("admin.reports", compact('users', 'websites', 'quizzes', 'submissions', 'stats'));
  }


}
