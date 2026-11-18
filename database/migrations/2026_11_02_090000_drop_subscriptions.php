<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The recurring-subscription feature was removed. Databases that already ran the original
 * migration still have its tables, so this clears them out (and is a no-op on fresh installs).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('orders', 'subscription_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropConstrainedForeignId('subscription_id');
            });
        }

        Schema::dropIfExists('subscription_items');
        Schema::dropIfExists('subscriptions');

        // Settings and vouchers that only made sense for subscriptions.
        DB::table('settings')->whereIn('key', ['subscription_discount_percent', 'subscription_lead_days'])->delete();
        DB::table('voucher_rules')->where('trigger', 'subscription_started')->delete();
    }

    public function down(): void
    {
        // Intentionally empty: the feature is gone, so there is nothing to restore.
    }
};
