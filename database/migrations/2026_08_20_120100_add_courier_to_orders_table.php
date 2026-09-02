<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('courier_code')->nullable()->after('longitude');
            $table->string('courier_service_code')->nullable()->after('courier_code');
            $table->string('courier_name')->nullable()->after('courier_service_code');
            $table->string('courier_service_name')->nullable()->after('courier_name');
            $table->string('shipping_duration')->nullable()->after('courier_service_name');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'courier_code', 'courier_service_code', 'courier_name', 'courier_service_name', 'shipping_duration',
            ]);
        });
    }
};
