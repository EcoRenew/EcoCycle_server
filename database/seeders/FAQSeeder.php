<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FAQSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'What materials can I recycle?',
                'answer' => 'We accept paper, cardboard, plastic bottles/jugs, glass bottles/jars, and metal cans.',
                'category' => 'recycling',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'How do I prepare my items?',
                'answer' => 'Rinse containers, remove residue, flatten cardboard, and remove caps unless specified otherwise.',
                'category' => 'recycling',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'What items can I donate?',
                'answer' => 'Gently used clothing, household items, electronics, and furniture in good working condition.',
                'category' => 'donations',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'question' => 'How do I schedule a donation pickup?',
                'answer' => 'Schedule via website or app, list items and select a convenient pickup time.',
                'category' => 'donations',
                'display_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate([
                'question' => $faq['question'],
            ], $faq);
        }
    }
}
