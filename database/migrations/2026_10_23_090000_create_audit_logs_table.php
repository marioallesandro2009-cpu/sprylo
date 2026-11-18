<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            // Who did it. The name/email/role are copied so the trail stays readable if the account is later renamed or deleted.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();
            $table->string('user_role', 30)->nullable();
            // What happened: login, logout, login_failed, created, updated, deleted.
            $table->string('action', 30);
            $table->string('subject_type', 60)->nullable(); // human label, e.g. "Product", "Order"
            $table->string('subject_id', 60)->nullable();
            $table->string('subject_label')->nullable();    // e.g. the product name or order number
            $table->text('description');
            $table->json('changes')->nullable();            // {field: [old, new]}
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
