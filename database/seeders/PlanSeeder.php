<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

/**
 * Seeds three subscription tiers (monthly / yearly / lifetime) in BDT. Prices
 * and copy are placeholders — adjust from Admin → Plans. Plan slugs are set
 * explicitly (Plan has no auto-slug), so updateOrCreate keeps this idempotent.
 */
class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $base = [
            ['bn' => 'বিজ্ঞাপনমুক্ত পড়ার অভিজ্ঞতা', 'en' => 'Ad-free reading'],
            ['bn' => 'সব প্রিমিয়াম কনটেন্ট', 'en' => 'All premium content'],
            ['bn' => 'বিশেষ নিউজলেটার', 'en' => 'Exclusive newsletter'],
        ];

        $plans = [
            [
                'slug' => 'monthly',
                'name' => ['bn' => 'মাসিক', 'en' => 'Monthly'],
                'description' => ['bn' => 'প্রতি মাসে নবায়নযোগ্য, যেকোনো সময় বাতিলযোগ্য।', 'en' => 'Renews monthly, cancel anytime.'],
                'price' => 199,
                'interval' => 'month',
                'interval_count' => 1,
                'position' => 1,
                'features' => $base,
            ],
            [
                'slug' => 'yearly',
                'name' => ['bn' => 'বার্ষিক', 'en' => 'Yearly'],
                'description' => ['bn' => 'বছরে একবার—দুই মাস কার্যত ফ্রি!', 'en' => 'Billed yearly — effectively two months free!'],
                'price' => 1990,
                'interval' => 'year',
                'interval_count' => 1,
                'position' => 2,
                'features' => array_merge($base, [['bn' => '২ মাস ফ্রি', 'en' => '2 months free']]),
            ],
            [
                'slug' => 'lifetime',
                'name' => ['bn' => 'আজীবন', 'en' => 'Lifetime'],
                'description' => ['bn' => 'একবার পরিশোধ করে আজীবন প্রিমিয়াম সুবিধা।', 'en' => 'Pay once, premium forever.'],
                'price' => 9990,
                'interval' => 'lifetime',
                'interval_count' => 1,
                'position' => 3,
                'features' => array_merge($base, [['bn' => 'আজীবন অ্যাক্সেস', 'en' => 'Lifetime access']]),
            ],
        ];

        foreach ($plans as $data) {
            Plan::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'currency' => 'BDT',
                    'interval' => $data['interval'],
                    'interval_count' => $data['interval_count'],
                    'features' => $data['features'],
                    'is_active' => true,
                    'position' => $data['position'],
                ],
            );
        }
    }
}
