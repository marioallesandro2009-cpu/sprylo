<?php

namespace Database\Seeders;

use App\Models\PromoCode;
use Illuminate\Database\Seeder;

class PromoCodeSeeder extends Seeder
{
    public function run(): void
    {
        $codes = [
            [
                'code' => 'WELCOME10',
                'type' => 'percent',
                'value' => 10,
                'min_subtotal' => null,
                'expires_at' => null,
                'is_active' => true,
            ],
            [
                'code' => 'SPRYLO50K',
                'type' => 'fixed',
                'value' => 50000,
                'min_subtotal' => 500000,
                'expires_at' => null,
                'is_active' => true,
            ],
        ];

        foreach ($codes as $code) {
            PromoCode::updateOrCreate(['code' => $code['code']], $code);
        }
    }
}
