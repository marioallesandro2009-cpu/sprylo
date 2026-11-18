<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // What the rule targets: one product (optionally narrowed to a variant), a whole category, or a brand.
            $table->string('scope', 20);
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('type', 20); // percent | amount
            $table->decimal('value', 12, 2);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->boolean('is_flash')->default(false);
            $table->unsignedInteger('quota')->nullable(); // units sellable at the discounted price; null = unlimited
            $table->unsignedInteger('sold')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'starts_at', 'ends_at']);
        });

        // The winning discounted price is materialised next to sale_price so listing filters/sorts
        // (plain SQL) keep working; DiscountSync keeps these columns in step with the schedule.
        foreach (['products', 'product_variants'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->decimal('promo_price', 12, 2)->nullable();
                $table->foreignId('promo_discount_id')->nullable()->constrained('discounts')->nullOnDelete();
            });
        }

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('discount_id')->nullable()->constrained('discounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('discount_id');
        });

        foreach (['products', 'product_variants'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('promo_discount_id');
                $table->dropColumn('promo_price');
            });
        }

        Schema::dropIfExists('discounts');
    }
};
