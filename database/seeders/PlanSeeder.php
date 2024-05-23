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
        'name' => 'Musaad Growing',
        'slug' => 'musaadgrowing',
        // 'stripe_plan' => 'price_1NnklHF3E3HoHaeo8svzG9nJ',// testing S
        'stripe_plan' => 'price_1OGPvTCD6W8jDb40wG9Mde9K',
        'paypal_plan' => '',
        'price' => 35,
        'best_seller' => false,
        'description' => 'Musaad Growing Package'
      ],
      [
        'id' => 2,
        'name' => 'Musaad Advance',
        'slug' => 'musaadadvance',
        'stripe_plan' => 'price_1NnklkF3E3HoHaeo40dDw7eW',
        'paypal_plan' => 'P-8H312152E29145839MT6COVA',
        'price' => 70,
        'best_seller' => false,
        'description' => 'Musaad Advance Package'
      ],
      [
        'id' => 3,
        'name' => 'Premium',
        'slug' => 'premium',
        // 'stripe_plan' => 'price_1OGPvjCD6W8jDb40K82egWfe',// testing S
        'stripe_plan' => 'price_1OGQ0dCD6W8jDb40TBqw9r48',
        'paypal_plan' => 'P-4GM66517EL458761VMT6CPCY',
        'price' => 186,
        'best_seller' => true,
        'description' => 'Premium Package'
      ],
      [
        'id' => 4,
        'name' => 'Musaad Growing',
        'slug' => 'musaadgrowing',
        // 'stripe_plan' => 'price_1NnklHF3E3HoHaeo8svzG9nJ',// testing S
        'stripe_plan' => 'price_1OGPcwCD6W8jDb40NeuHfffX',
        'price' => 294,
        'best_seller' => false,
        'description' => 'Musaad Growing Package'
      ],
      [
        'id' => 5,
        'name' => 'Musaad Advance',
        'slug' => 'musaadadvance',
        // 'stripe_plan' => 'price_1NnklkF3E3HoHaeo40dDw7eW',// testing S
        'stripe_plan' => 'price_1NnklkF3E3HoHaeo40dDw7eW',
        'paypal_plan' => 'P-8H312152E29145839MT6COVA',
        'price' => 588,
        'best_seller' => false,
        'description' => 'Musaad Advance Package'
      ],
      [
        'id' => 6,
        'name' => 'Musaad Success',
        'slug' => 'musaadsuccess',
        // 'stripe_plan' => 'price_1NnkmDF3E3HoHaeoZyVzTrdx',// testing S
        'stripe_plan' => 'price_1NnkmDF3E3HoHaeoZyVzTrdx',
        'paypal_plan' => 'P-4GM66517EL458761VMT6CPCY',
        'price' => 1260,
        'best_seller' => true,
        'description' => 'Musaad Success Package'
      ]
    ];

    foreach ($plans as $plan) {
      Plan::create($plan);
    }

  }
}
