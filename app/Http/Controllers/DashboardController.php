<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use App\Models\Quiz;
use Carbon\Carbon;
use App\Models\User;

class DashboardController extends Controller {


  public function reports() {

    $users = DB::table("users")->count();
    $websites_count = DB::table("websites")->count();
    $quizzes = DB::table("quizzes")->count();
    $submissions = DB::table("submissions")->count();
    $stats = [];

    // $stats['inactive_payments'] = DB::table("payments")->where("status", "!=", "completed")->count();
    // $stats['active_payments'] = DB::table("payments")->where("status", "completed")->count();

    $payments = DB::table("payments")->select("created_at", "is_annual", "status")->get();

    $stats['active_payments'] = 0;

    foreach ($payments as $pymnt) {
      if ($pymnt->status == "completed") {
        $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $pymnt->created_at);
        if ($pymnt->is_annual == 1) {
          if (!$created_at->greaterThan(Carbon::now()->subYears(1))) {
            $stats['active_payments'] += 1;
          }
        } else {
          if (!$created_at->greaterThan(Carbon::now()->addMonths(1))) {
            $stats['active_payments'] += 1;
          }
        }
      }
    }

    $stats['inactive_payments'] = count($payments) - $stats['active_payments'];

    $stats['inactive_users'] = DB::table("users")
      ->where("took_trial", 1)
      ->where("status", "!=", "admin");
    $stats['inactive_users'] = $stats['inactive_users']->where(function ($query) {
      $query->where("status", "inactive");
      $query->orWhereDate('subscription_end', '<', now());
    });
    $stats['inactive_users'] = $stats['inactive_users']->count();

    // $paid_users = DB::table("users")
    //   ->select("users.name", DB::raw('COUNT(payments.user_id) as paymentsCount'))
    //   ->join("payments", "payments.user_id", "=", "users.id")
    //   // ->groupBy('user_id')
    //   ->groupBy('users.id')
    //   ->where("payments.status", "completed")
    //   ->get();

    // $stats['paid_users'] = DB::table("users")->where("status", "!=", "completed")->count();
    // $stats['paid_users'] = count($paid_users) - $stats['repaid_users'];

    $stats['active_users'] = COUNT(DB::select("SELECT U.id FROM users U INNER JOIN payments P ON U.id = P.user_id WHERE P.status = 'completed' AND (U.subscription_end > NOW() OR U.status = 'admin') GROUP BY U.id"));

    $stats['trial_users'] = $users - ($stats['active_users'] + $stats['inactive_users']);

    // select * from `users` inner join `payments` on `users`.`id` = `payments`.`user_id` where `took_trial` = ? group by `users`.`id
    // $stats['active_users'] = DB::table("users")
    //   ->where("took_trial", "1")
    //   ->join("payments", "users.id", "=", "payments.user_id")
    //   ->groupBy("users.id")
    //   ->count();
    // count($paid_users->filter(function ($paid) {
    //   return $paid->paymentsCount > 1;
    // }));

    /*
      select users.id, count(*) * 100.0 / sum(count(*)) over()
      from users
      group by status
    */

    // ->select("plans.id", "plans.is_yearly", "plans.name", DB::raw("count(payments.package_id) AS subs_count"), DB::raw("count(payments.package_id) * 100.0 / sum(count(payments.package_id)) over() AS subs"))
    // $plans = DB::table("plans")
    //   ->leftJoin("payments", "plans.id", "=", "package_id")
    //   ->select("plans.id", "plans.is_yearly", "plans.name", DB::raw("count(payments.package_id) AS subs_count"), DB::raw("count(payments.package_id) * 100.0 / (select count(*) from payments) AS subs"))
    //   ->groupBy("plans.id")
    //   ->get();

    $plans = DB::table("plans")->select("id", "is_yearly", "name")->get()->keyBy("id");
    // SELECT U.id, U.name, P.package_id FROM users U LEFT JOIN payments P ON U.id = P.user_id GROUP By U.id Order BY P.created_at
    $subscribers = DB::table("users")
      ->select(DB::raw("users.name, payments.package_id, payments.is_annual, MAX(payments.created_at) AS payment_at"))
      ->leftJoin("payments", "payments.user_id", "=", "users.id")
      ->where("payments.status", "=", "completed")
      ->where(function ($query) {
        $query->where("users.subscription_end", ">", NOW());
        $query->orWhere("users.status", "=", "admin");
      })
      ->groupBy("users.id")
      ->whereNotNull("payments.package_id")
      ->get();
    // dd($subscribers);

    foreach ($plans as $plan) {
      $plan->subs = 0;
    }
    // dd($subscribers);
    foreach ($subscribers as $sub) {
      if ($sub->is_annual == 1) {
        $plans[$sub->package_id + 3]->subs += 1;
      } else {
        $plans[$sub->package_id]->subs += 1;
      }
    }

    // dd(round(microtime(true) - LARAVEL_START, 3));// 3.5

    return view("admin.reports", compact(
      'users',
      'websites_count',
      'quizzes',
      'submissions',
      'plans',
      'stats',
    ));
  }

  public function payments_reports() {

    $payments = Payment::with("user")->paginate(20);

    // ->select("plans.id", "plans.is_yearly", "plans.name", DB::raw("count(payments.package_id) AS subs_count"), DB::raw("count(payments.package_id) * 100.0 / sum(count(payments.package_id)) over() AS subs"))
    $plans = DB::table("plans")
      ->leftJoin("payments", "plans.id", "=", "package_id")
      ->select("plans.id", "plans.is_yearly", "plans.name", DB::raw("count(payments.package_id) AS subs_count"), DB::raw("count(payments.package_id) * 100.0 / (select count(*) from payments) AS subs"))
      ->groupBy("plans.id")
      ->get();

    return view("admin.payment-reports", compact(
      'payments',
      'plans',
    ));
  }

  public function quizzes_reports() {

    $quizzes = Quiz::join("websites", "websites.id", "=", "quizzes.website_id")
      ->join("users", "users.id", "=", "websites.user_id")
      ->select("users.id AS user_id", "users.name AS user_name", "quizzes.id", "quizzes.status")
      ->paginate(20);

    return view("admin.quizzes-reports", compact(
      'quizzes',
    ));
  }
}
