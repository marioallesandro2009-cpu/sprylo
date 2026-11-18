<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // morning | afternoon | evening — the time window the customer picked for requested_delivery_date.
            $table->string('requested_delivery_slot', 20)->nullable()->after('requested_delivery_date');
            $table->index(['requested_delivery_date', 'requested_delivery_slot'], 'orders_delivery_schedule_index');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_delivery_schedule_index');
            $table->dropColumn('requested_delivery_slot');
        });
    }
};
