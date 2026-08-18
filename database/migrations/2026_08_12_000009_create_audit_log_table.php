<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 100);
            $table->string('affected_table', 50);
            $table->unsignedBigInteger('record_id')->nullable();
            $table->json('details')->nullable();
            $table->string('source_ip', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index('created_at', 'idx_audit_date');
            $table->index('user_id');
            $table->index(['affected_table', 'record_id']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};
