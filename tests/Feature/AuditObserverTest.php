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
        $this->assertSame('Bebidas', $rows->first()->details['name']);
    }

    public function test_updating_a_model_logs_only_changed_attributes(): void
    {
        $category = Category::create(['name' => 'Panaderia', 'is_active' => true]);

        $category->update(['name' => 'Panadería']);

        $log = $this->auditRows('UPDATED', 'categories', $category->id)->first();

        $this->assertNotNull($log);
        $this->assertSame('Panaderia', $log->details['before']['name']);
        $this->assertSame('Panadería', $log->details['after']['name']);
        $this->assertArrayNotHasKey('is_active', $log->details['after']);
        $this->assertArrayNotHasKey('updated_at', $log->details['after']);
    }

    public function test_deleting_a_model_is_audited_with_snapshot(): void
    {
        $category = Category::create(['name' => 'Lacteos', 'is_active' => true]);

        $category->delete();

        $log = $this->auditRows('DELETED', 'categories', $category->id)->first();

        $this->assertNotNull($log);
        $this->assertSame('Lacteos', $log->details['name']);
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

    public function test_password_is_never_exposed_in_audit_details(): void
    {
        $user = User::factory()->create([
            'password' => 'super-secret-123',
        ]);

        $created = $this->auditRows('CREATED', 'users', $user->id)->first();
        $this->assertNotNull($created);
        $this->assertArrayNotHasKey('password', $created->details);

        $user->update(['password' => 'other-secret-456']);

        $updated = $this->auditRows('UPDATED', 'users', $user->id)->first();
        $this->assertNotNull($updated);
        $this->assertSame('[oculto]', $updated->details['before']['password']);
        $this->assertSame('[oculto]', $updated->details['after']['password']);
        $this->assertStringNotContainsString('super-secret', json_encode($updated->details));
        $this->assertStringNotContainsString('other-secret', json_encode($updated->details));
    }

    public function test_registered_models_are_audited(): void
    {
        Role::create(['name' => 'SUPERVISOR', 'description' => null, 'is_active' => true]);

        $this->assertSame(
            1,
            $this->auditRows('CREATED', 'roles', Role::where('name', 'SUPERVISOR')->value('id'))->count(),
        );
    }
}
