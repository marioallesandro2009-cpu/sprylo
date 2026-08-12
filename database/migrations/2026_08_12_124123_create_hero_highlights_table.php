<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_highlights', function (Blueprint $table) {
            $table->id();
            $table->string('position')->unique(); // purple | teal | orange | green | black — fixed bento grid slots
            $table->string('eyebrow');
            $table->string('heading');
            $table->string('image');
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_highlights');
    }
};
