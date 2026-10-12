<?php

if (! function_exists('rupiah')) {
    function rupiah(float|int|string $amount): string
    {
        return 'Rp '.number_format((float) $amount, 0, ',', '.');
    }
}

if (! function_exists('weight_label')) {
    function weight_label(int|string|null $grams): string
    {
        $grams = (int) $grams;

        if ($grams >= 1000) {
            return rtrim(rtrim(number_format($grams / 1000, 2, '.', ''), '0'), '.').' kg';
        }

        return $grams.' g';
    }
}

if (! function_exists('cms')) {
    /**
     * Teks/gambar yang bisa diedit dari admin (Admin > Page Content).
     * Pakai: {{ cms('home.trending.title', 'Trending Products') }}
     * Placeholder: cms('shop.toolbar.showing', 'Showing :count products', ['count' => 12])
     */
    function cms(string $key, string $default = '', array $replace = []): string
    {
        static $map = null;

        if ($map === null) {
            try {
                $map = \App\Models\PageContent::map();
            } catch (\Throwable) {
                $map = [];
            }
        }

        $value = $map[$key] ?? null;
        $text = ($value !== null && $value !== '') ? (string) $value : $default;

        foreach ($replace as $name => $val) {
            $text = str_replace(':'.$name, (string) $val, $text);
        }

        return $text;
    }
}