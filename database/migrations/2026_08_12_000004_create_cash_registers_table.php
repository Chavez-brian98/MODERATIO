<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('responsible_id')->nullable()->constrained('users');
            $table->string('shift', 20)->nullable();
            $table->decimal('opening_amount', 10, 2);
            $table->decimal('theoretical_closing_amount', 10, 2)->nullable();
            $table->decimal('actual_closing_amount', 10, 2)->nullable();
            $table->decimal('difference', 10, 2)->nullable();
            $table->text('closing_notes')->nullable();
            $table->enum('status', ['OPEN', 'CLOSED'])->default('OPEN');
            $table->timestamp('opening_date')->useCurrent();
            $table->timestamp('closing_date')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status'], 'idx_register_user_status');
            $table->index('opening_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};
