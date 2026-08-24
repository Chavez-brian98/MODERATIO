<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['user_has_roles', 'role_has_permissions', 'user_has_permissions'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn('updated_at');
            });
        }
    }

    public function down(): void
    {
        foreach (['user_has_roles', 'role_has_permissions', 'user_has_permissions'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->timestamp('updated_at')->nullable();
            });
        }
    }
};
