<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL does not rename indexes when a table is renamed, so the
        // unique index on `position` must be dropped by its original
        // (pre-rename) name — `dropUnique(['position'])` would guess
        // `hero_slides_position_unique`, which never existed.
        Schema::table('hero_highlights', function (Blueprint $table) {
            $table->dropUnique('hero_highlights_position_unique');
        });

        Schema::rename('hero_highlights', 'hero_slides');

        Schema::table('hero_slides', function (Blueprint $table) {
            $table->dropColumn('position');

            $table->string('size')->default('small')->after('id'); // large | small
            $table->string('theme')->default('indigo')->after('size');
            $table->text('description')->nullable()->after('heading');
            $table->string('cta_label')->nullable()->after('description');
            $table->unsignedInteger('sort_order')->default(0)->after('product_id');
            $table->boolean('is_active')->default(true)->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->dropColumn(['size', 'theme', 'description', 'cta_label', 'sort_order', 'is_active']);
            $table->string('position')->unique()->after('id');
        });

        Schema::rename('hero_slides', 'hero_highlights');
    }
};
