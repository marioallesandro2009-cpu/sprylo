<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            AdminUserSeeder::class,
            SuperAdminSeeder::class,
            PageHeadingSettingSeeder::class,
            LabelSettingSeeder::class,
            SettingSeeder::class,
            DynamicContentSeeder::class,
            PageMetaSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
            HeroSlideSeeder::class,
            PromoBannerSeeder::class,
            ReviewSeeder::class,
            FaqSeeder::class,
            FooterLinkSeeder::class,
            ContactMessageSeeder::class,
            PromoCodeSeeder::class,
        ]);
    }
}
