<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('has_insurance')->default(false)->after('promo_discount');
            $table->decimal('insurance_fee', 12, 2)->default(0)->after('has_insurance');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['has_insurance', 'insurance_fee']);
        });
    }
};
