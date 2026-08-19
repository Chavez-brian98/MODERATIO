<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['payment_method', 'created_at']);
            $table->dropColumn('payment_method');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->enum('payment_method', ['CASH', 'CARD', 'TRANSFER', 'MIXED'])->default('CASH')->after('change_due');
            $table->index(['payment_method', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['payment_method', 'created_at']);
            $table->dropColumn('payment_method');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->enum('payment_method', ['CASH', 'CARD', 'TRANSFER'])->default('CASH')->after('change_due');
            $table->index(['payment_method', 'created_at']);
        });
    }
};
