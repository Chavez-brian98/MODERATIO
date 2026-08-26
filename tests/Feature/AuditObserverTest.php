<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class AuditObserverTest extends TestCase
{
    use RefreshDatabase;

    private function auditRows(string $action, string $table, int $recordId): Collection
    {
        return AuditLog::query()
            ->where('action', $action)
            ->where('affected_table', $table)
            ->where('record_id', $recordId)
            ->get();
    }

    public function test_creating_a_model_is_audited_automatically(): void
    {
        $category = Category::create(['name' => 'Bebidas', 'is_active' => true]);

        $rows = $this->auditRows('CREATED', 'categories', $category->id);

        $this->assertSame(1, $rows->count());
        $this->assertNull($rows->first()->old_values);
        $this->assertSame('Bebidas', $rows->first()->new_values['name']);
    }

    public function test_updating_a_model_logs_only_changed_attributes(): void
    {
        $category = Category::create(['name' => 'Panaderia', 'is_active' => true]);

        $category->update(['name' => 'Panadería']);

        $log = $this->auditRows('UPDATED', 'categories', $category->id)->first();

        $this->assertNotNull($log);
        $this->assertSame('Panaderia', $log->old_values['name']);
        $this->assertSame('Panadería', $log->new_values['name']);
        $this->assertArrayNotHasKey('is_active', $log->new_values);
        $this->assertArrayNotHasKey('updated_at', $log->new_values);
    }

    public function test_deleting_a_model_is_audited_with_snapshot(): void
    {
        $category = Category::create(['name' => 'Lacteos', 'is_active' => true]);

        $category->delete();

        $log = $this->auditRows('DELETED', 'categories', $category->id)->first();

        $this->assertNotNull($log);
        $this->assertSame('Lacteos', $log->old_values['name']);
        $this->assertNull($log->new_values);
    }

    public function test_toggle_keeps_single_semantic_entry_without_updated_duplicate(): void
    {
        $this->signIn();

        $category = Category::create(['name' => 'Snacks', 'is_active' => true]);

        $this->patch(route('categories.toggle', $category))->assertRedirect();

        $all = AuditLog::query()
            ->where('affected_table', 'categories')
            ->where('record_id', $category->id)
            ->orderBy('id')
            ->get();

        $this->assertSame(2, $all->count());
        $this->assertSame('CREATED', $all[0]->action);
        $this->assertSame('TOGGLED', $all[1]->action);
        $this->assertTrue($category->fresh()->is_active === false);
    }

    public function test_password_is_never_exposed_in_audit_values(): void
    {
        $user = User::factory()->create([
            'password' => 'super-secret-123',
        ]);

        $created = $this->auditRows('CREATED', 'users', $user->id)->first();
        $this->assertNotNull($created);
        $this->assertArrayNotHasKey('password', $created->new_values);

        $user->update(['password' => 'other-secret-456']);

        $updated = $this->auditRows('UPDATED', 'users', $user->id)->first();
        $this->assertNotNull($updated);
        $this->assertSame('[oculto]', $updated->old_values['password']);
        $this->assertSame('[oculto]', $updated->new_values['password']);
        $this->assertStringNotContainsString('super-secret', json_encode([$updated->old_values, $updated->new_values]));
        $this->assertStringNotContainsString('other-secret', json_encode([$updated->old_values, $updated->new_values]));
    }

    public function test_registered_models_are_audited(): void
    {
        Role::create(['name' => 'SUPERVISOR', 'description' => null, 'is_active' => true]);

        $this->assertSame(
            1,
            $this->auditRows('CREATED', 'roles', Role::where('name', 'SUPERVISOR')->value('id'))->count(),
        );
    }

    public function test_audit_log_persists_created_at_in_app_timezone(): void
    {
        Category::create(['name' => 'Temporal', 'is_active' => true]);

        $log = AuditLog::query()->latest('id')->first();

        $this->assertNotNull($log);
        $this->assertNotNull($log->created_at);
        $this->assertTrue($log->created_at->diffInSeconds(now()) < 5);
    }
}
