<?php

namespace App\Http\Middleware;

use App\Support\DiscountSync;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Scheduled discounts start and stop on their own: before a request is served, refresh the
 * materialised promo prices if a rule's start or end time has passed since the last sync.
 * (The same sync also runs every minute from the scheduler on servers that have cron.)
 */
class SyncDiscounts
{
    public function handle(Request $request, Closure $next): Response
    {
        DiscountSync::ensureFresh();

        return $next($request);
    }
}
