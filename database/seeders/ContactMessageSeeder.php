<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use Illuminate\Database\Seeder;

class ContactMessageSeeder extends Seeder
{
    public function run(): void
    {
        $messages = [
            [
                'first_name' => 'Mira', 'last_name' => 'Kapoor', 'email' => 'mira.kapoor@example.com',
                'phone' => '+1 (415) 555 0123', 'topic' => 'Order — tracking, change, or cancellation',
                'order_number' => 'SPR-204418', 'message' => 'Hi, my order shows shipped but the tracking link has not updated in 3 days. Can you check the status?',
                'is_read' => false,
            ],
            [
                'first_name' => 'Devan', 'last_name' => 'Rios', 'email' => 'devan.rios@example.com',
                'phone' => null, 'topic' => 'Warranty claim or product issue',
                'order_number' => 'SPR-198832', 'message' => 'The HomePod I bought two months ago has started making a rattling noise on bass-heavy tracks. Is this covered under warranty?',
                'is_read' => true,
            ],
            [
                'first_name' => 'Priya', 'last_name' => 'Chen', 'email' => 'priya.chen@example.com',
                'phone' => '+1 (628) 555 0199', 'topic' => 'Trade / wholesale inquiry',
                'order_number' => null, 'message' => "We're a corporate reseller looking to order 25+ units of the Apple Watch Series 9 quarterly. Could someone from the trade desk reach out?",
                'is_read' => false,
            ],
        ];

        foreach ($messages as $message) {
            ContactMessage::create($message);
        }
    }
}
