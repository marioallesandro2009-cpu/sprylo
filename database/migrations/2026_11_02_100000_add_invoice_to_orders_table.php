<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Assigned the first time an invoice is generated, then never changes: INV-YYMM-0001, INV-YYMM-0002, … (numbers made before 13 Nov 2026 read INV-YYYYMM-0001 and keep it)
            $table->string('invoice_number', 30)->nullable()->unique()->after('order_number');
            $table->timestamp('invoiced_at')->nullable()->after('invoice_number');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['invoice_number']);
            $table->dropColumn(['invoice_number', 'invoiced_at']);
        });
    }
};
