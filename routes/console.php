<?php

use App\Support\DiscountSync;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Starts/ends scheduled discounts and flash sales on time even when nobody is browsing the shop.
Schedule::call(fn () => DiscountSync::run())->everyMinute()->name('discounts:sync')->withoutOverlapping();
