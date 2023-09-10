<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder {


  public function run() {
    $plans = [
      [
        'id' => 1,
        'name' => 'Basic',
        'slug' => 'basic',
        'stripe_plan' => 'price_1NnklHF3E3HoHaeo8svzG9nJ',
        'paypal_plan' => 'P-2W419892AD2451641MT6CNCA',
        'price' => 37,
        'best_seller' => false,
        'description' => 'Basic Package'
      ],
      [
        'id' => 2,
        'name' => 'Pro',
        'slug' => 'pro',
        'stripe_plan' => 'price_1NnklkF3E3HoHaeo40dDw7eW',
        'paypal_plan' => 'P-8H312152E29145839MT6COVA',
        'price' => 74,
        'best_seller' => false,
        'description' => 'Pro Package'
      ],
      [
        'id' => 3,
        'name' => 'Premium',
        'slug' => 'premium',
        'stripe_plan' => 'price_1NnkmDF3E3HoHaeoZyVzTrdx',
        'paypal_plan' => 'P-4GM66517EL458761VMT6CPCY',
        'price' => 186,
        'best_seller' => true,
        'description' => 'Premium Package'
      ]
    ];

    foreach ($plans as $plan) {
      Plan::create($plan);
    }

  }
}
