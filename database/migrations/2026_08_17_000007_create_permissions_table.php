<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained()->cascadeOnDelete();
            $table->foreignId('action_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100)->unique();
            $table->string('display_name', 100);
            $table->string('description', 255)->nullable();
            $table->timestamps();

            $table->unique(['resource_id', 'action_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
