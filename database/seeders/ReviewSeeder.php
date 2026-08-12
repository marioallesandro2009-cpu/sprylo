<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            5 => [
                'Exactly as described — build quality is excellent and it arrived well packaged. Would order again.',
                'Best purchase I\'ve made this year. Setup was quick and it just works.',
                'Genuinely impressed. Worth every dollar, and support answered my questions within the hour.',
                'Five stars — fast shipping, great packaging, and the product itself over-delivers.',
            ],
            4 => [
                'Really solid overall. A couple of minor quirks but nothing that affects daily use.',
                'Great value for the price. Does everything I need it to.',
                'Happy with this — shipping was fast and it matches the listing photos closely.',
            ],
            3 => [
                'Does the job, but I expected a bit more polish at this price point.',
                'Decent, though the battery life is shorter than advertised in my experience.',
            ],
        ];

        $reviewers = [
            'Mira K.', 'Devan R.', 'Priya C.', 'Jordan L.', 'Sam T.', 'Alex W.',
            'Noor H.', 'Casey B.', 'Riley M.', 'Taylor S.', 'Morgan P.', 'Jamie F.',
        ];

        $ratingPattern = [5, 5, 4, 4, 5, 3];

        Product::all()->each(function (Product $product) use ($templates, $reviewers, $ratingPattern) {
            $product->reviews()->delete();

            $reviewCount = 3 + ($product->id % 3);

            for ($i = 0; $i < $reviewCount; $i++) {
                $rating = $ratingPattern[($product->id + $i) % count($ratingPattern)];
                $reviewer = $reviewers[($product->id * 3 + $i) % count($reviewers)];
                $comment = $templates[$rating][($product->id + $i) % count($templates[$rating])];

                $review = $product->reviews()->make([
                    'name' => $reviewer,
                    'rating' => $rating,
                    'comment' => $comment,
                ]);
                $review->created_at = now()->subDays(($i + 1) * 3 + $product->id);
                $review->updated_at = $review->created_at;
                $review->save();
            }
        });
    }
}
