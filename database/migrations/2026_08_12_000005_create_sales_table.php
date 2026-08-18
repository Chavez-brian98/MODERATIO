<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_register_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->string('ticket_number', 30)->unique();
            $table->decimal('total', 10, 2);
            $table->decimal('amount_received', 10, 2);
            $table->decimal('change_due', 10, 2);
            $table->enum('payment_method', ['CASH', 'CARD', 'TRANSFER'])->default('CASH');
            $table->enum('status', ['COMPLETED', 'CANCELLED', 'PARTIALLY_RETURNED'])->default('COMPLETED');
            $table->text('observations')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index('created_at', 'idx_sales_date');
            $table->index('status');
            $table->index(['status', 'created_at']);
            $table->index(['payment_method', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
