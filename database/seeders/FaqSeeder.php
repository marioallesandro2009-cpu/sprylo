<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Where is my order?',
                'answer' => 'Every order ships with a tracking number sent by email within 2 hours of dispatch. Use the order tracker with your email + order number for live status.',
            ],
            [
                'question' => 'How do returns work?',
                'answer' => '30 days, no questions asked. Initiate from your account → orders → return. We email a prepaid label; drop off at any carrier point. Refund hits your card 3 – 5 business days after we receive it.',
            ],
            [
                'question' => 'Do you ship internationally?',
                'answer' => 'Yes — to 48 countries. Free standard shipping on orders over $50 to the US, EU, UK, Canada, and Australia. Other destinations carry a flat shipping fee shown at checkout.',
            ],
            [
                'question' => 'What is the warranty?',
                'answer' => 'Every order ships with a 2-year limited warranty in addition to the manufacturer\'s. Optional extended cover (3 or 5 years) can be added at checkout from $39.',
            ],
        ];

        foreach ($faqs as $index => $faq) {
            Faq::updateOrCreate(
                ['question' => $faq['question']],
                ['answer' => $faq['answer'], 'sort_order' => $index]
            );
        }
    }
}
