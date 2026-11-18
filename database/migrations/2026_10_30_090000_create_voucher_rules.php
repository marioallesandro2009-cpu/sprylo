<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rules that automatically hand a customer a personal, single-use voucher when something happens.
        Schema::create('voucher_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('trigger', 30);                 // first_order | order_milestone
            $table->unsignedSmallInteger('milestone_every')->nullable(); // order_milestone: every Nth order
            $table->string('type', 20);                    // percent | fixed
            $table->decimal('value', 12, 2);
            $table->decimal('min_subtotal', 14, 2)->nullable();
            $table->unsignedSmallInteger('valid_days')->default(30);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('issued_count')->default(0);
            $table->timestamps();
        });

        Schema::table('promo_codes', function (Blueprint $table) {
            $table->foreignId('voucher_rule_id')->nullable()->constrained('voucher_rules')->nullOnDelete();
            $table->string('issued_to_email')->nullable();
            $table->unsignedInteger('usage_limit')->nullable();   // null = unlimited
            $table->unsignedInteger('used_count')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('voucher_rule_id');
            $table->dropColumn(['issued_to_email', 'usage_limit', 'used_count']);
        });
        Schema::dropIfExists('voucher_rules');
    }
};
