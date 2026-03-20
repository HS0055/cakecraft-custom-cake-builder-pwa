<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'How far in advance should I place my custom cake order?',
                'answer' => 'We recommend placing custom cake orders at least 3-5 days in advance to ensure availability, especially during weekends and holidays.',
                'sort_order' => 1,
            ],
            [
                'question' => 'Do you offer gluten-free or vegan options?',
                'answer' => 'Yes! You can filter our cake flavors to find gluten-free and vegan options. Please note that while we take precautions, our kitchen does process wheat and dairy.',
                'sort_order' => 2,
            ],
            [
                'question' => 'Can I change my order after it has been placed?',
                'answer' => 'Changes to orders (including flavor, size, or design) can be made up to 48 hours before your specified delivery date. Contact our support team directly to make modifications.',
                'sort_order' => 3,
            ],
            [
                'question' => 'How do I store my cake once I receive it?',
                'answer' => 'Most buttercream cakes should be kept refrigerated but are best enjoyed at room temperature. We recommend taking the cake out of the fridge 1-2 hours before serving.',
                'sort_order' => 4,
            ],
            [
                'question' => 'Do you deliver?',
                'answer' => 'Yes! We offer local delivery within a 20-mile radius. Delivery fees are calculated at checkout based on your exact location.',
                'sort_order' => 5,
            ]
        ];

        foreach ($faqs as $faq) {
            \App\Models\Faq::firstOrCreate(
                ['question' => $faq['question']],
                array_merge($faq, ['is_active' => true])
            );
        }
    }
}
