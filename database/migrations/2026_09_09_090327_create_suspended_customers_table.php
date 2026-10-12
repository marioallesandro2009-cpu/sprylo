<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suspended_customers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable()->index();
            $table->string('reason')->nullable();
            $table->timestamp('suspended_until')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suspended_customers');
    }
};
