<?php

namespace Database\Seeders;

use App\Models\FooterLink;
use Illuminate\Database\Seeder;

class FooterLinkSeeder extends Seeder
{
    public function run(): void
    {
        $links = [
            'help' => ['My account', 'My order', 'Shipping policy', 'Help centre', 'Events', 'Popular products'],
            'resources' => ['Blog', 'Newsletter', 'Help centre', 'Support', 'Reviews'],
            'company' => [
                'About us' => '#',
                'Contact' => '/contact',
                'Login / Register' => '#',
                'Terms & conditions' => '#',
                'Privacy policy' => '#',
                'FAQs' => '#',
            ],
        ];

        foreach ($links as $group => $labels) {
            $index = 0;
            foreach ($labels as $label => $url) {
                if (is_int($label)) {
                    [$label, $url] = [$url, '#'];
                }

                FooterLink::updateOrCreate(
                    ['group' => $group, 'label' => $label],
                    ['url' => $url, 'sort_order' => $index++]
                );
            }
        }
    }
}
