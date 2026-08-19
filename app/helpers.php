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
