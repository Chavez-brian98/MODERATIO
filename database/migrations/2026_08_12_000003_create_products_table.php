<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained();
            $table->string('barcode', 50)->nullable()->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('sale_price', 10, 2);
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock')->default(5);
            $table->boolean('has_tax')->default(true);
            $table->decimal('tax_percentage', 5, 2)->default(13.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('name', 'idx_product_name');
            $table->index(['current_stock', 'min_stock'], 'idx_stock_alert');
            $table->index(['is_active', 'category_id']);
            $table->index(['is_active', 'current_stock']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
