<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('cash_register_id')->nullable()->constrained();
            $table->text('reason');
            $table->decimal('total_returned', 10, 2);
            $table->timestamp('created_at')->useCurrent();
            $table->index('sale_id');
            $table->index('cash_register_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
