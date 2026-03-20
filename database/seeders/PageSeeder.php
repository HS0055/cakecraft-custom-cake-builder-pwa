<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => '<h2>Welcome to Bawaneh Bakery</h2><p>We craft the finest cakes for all your special moments. Quality ingredients, passionate bakers, and a sprinkle of love.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'How to Order',
                'slug' => 'how-to-order',
                'content' => '<h2>Ordering is Easy!</h2><p>1. Browse our ready cakes or use the custom builder.<br>2. Add to cart and select your delivery date.<br>3. Checkout securely and await your sweet delivery!</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<h2>Privacy Policy</h2><p>We respect your privacy and protect your data. We only collect information necessary to process your orders and improve your experience.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'content' => '<h2>Terms of Service</h2><p>By using our storefront, you agree to our standard terms of service. All cake orders are final once preparation begins.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Refund Policy',
                'slug' => 'refund-policy',
                'content' => '<h2>Refund Policy</h2><p>Refunds are only issued if the order is cancelled 48 hours before the scheduled delivery time.</p>',
                'is_active' => true,
            ]
        ];

        foreach ($pages as $page) {
            \App\Models\Page::firstOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}
