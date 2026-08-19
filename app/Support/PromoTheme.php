<?php

namespace App\Support;

class PromoTheme
{
    // Shared color palette for hero slides + promo banners. Keys map to
    // `.theme-{key}` gradient classes in public/assets/css/styles.css.
    public const OPTIONS = [
        'indigo' => 'Indigo blue',
        'violet' => 'Violet purple',
        'teal' => 'Teal cyan',
        'orange' => 'Orange sunset',
        'green' => 'Emerald green',
        'slate' => 'Midnight slate',
        'rose' => 'Rose red',
    ];
}
