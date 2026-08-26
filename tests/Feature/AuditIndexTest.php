<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditIndexTest extends TestCase
{
    use RefreshDatabase;

    private function createLog(string $createdAt, string $action = 'CREATED'): AuditLog
    {
        $log = new AuditLog([
            'action' => $action,
            'affected_table' => 'categories',
            'record_id' => 1,
        ]);
        $log->created_at = Carbon::parse($createdAt);
        $log->save();

        return $log;
    }

    public function test_guests_cannot_view_audit_index(): void
    {
        $this->get(route('audit.index'))->assertRedirect();
    }

    public function test_index_lists_audit_logs(): void
    {
        $this->signIn();

        $log = $this->createLog('2026-08-20 10:00:00');

        $this->get(route('audit.index'))
            ->assertOk()
            ->assertSee('#'.$log->id);
    }

    public function test_index_filters_by_date_from(): void
    {
        $this->signIn();

        $older = $this->createLog('2026-08-01 09:00:00');
        $newer = $this->createLog('2026-08-20 09:00:00');

        $this->get(route('audit.index', ['date_from' => '2026-08-10']))
            ->assertOk()
            ->assertSee('#'.$newer->id)
            ->assertDontSee('#'.$older->id);
    }

    public function test_index_filters_by_date_to(): void
    {
        $this->signIn();

        $older = $this->createLog('2026-08-01 09:00:00');
        $newer = $this->createLog('2026-08-20 09:00:00');

        $this->get(route('audit.index', ['date_to' => '2026-08-10']))
            ->assertOk()
            ->assertSee('#'.$older->id)
            ->assertDontSee('#'.$newer->id);
    }

    public function test_index_filters_by_date_range(): void
    {
        $this->signIn();

        $before = $this->createLog('2026-08-01 09:00:00');
        $inside = $this->createLog('2026-08-15 09:00:00');
        $after = $this->createLog('2026-08-30 09:00:00');

        $this->get(route('audit.index', ['date_from' => '2026-08-10', 'date_to' => '2026-08-20']))
            ->assertOk()
            ->assertSee('#'.$inside->id)
            ->assertDontSee('#'.$before->id)
            ->assertDontSee('#'.$after->id);
    }

    public function test_index_ignores_invalid_dates(): void
    {
        $this->signIn();

        $first = $this->createLog('2026-08-01 09:00:00');
        $second = $this->createLog('2026-08-20 09:00:00');

        $this->get(route('audit.index', ['date_from' => 'no-es-una-fecha', 'date_to' => '99-99-9999']))
            ->assertOk()
            ->assertSee('#'.$first->id)
            ->assertSee('#'.$second->id);
    }

    public function test_guests_cannot_view_audit_detail(): void
    {
        $log = $this->createLog('2026-08-20 10:00:00');

        $this->get(route('audit.show', $log))->assertRedirect();
    }

    public function test_show_renders_full_detail_page(): void
    {
        $user = $this->signIn();

        $category = Category::query()->create([
            'name' => 'Bebidas',
            'description' => 'Refrescos y jugos',
            'is_active' => true,
        ]);

        $log = new AuditLog([
            'action' => 'UPDATED',
            'affected_table' => 'categories',
            'record_id' => $category->id,
            'old_values' => ['name' => 'Bebidas viejas'],
            'new_values' => ['name' => 'Bebidas'],
        ]);
        $log->created_at = Carbon::parse('2026-08-20 10:00:00');
        $log->save();

        $this->get(route('audit.show', $log))
            ->assertOk()
            ->assertSee($user->full_name)
            ->assertSee($user->email)
            ->assertSee('Categorías')
            ->assertSee('#'.$category->id)
            ->assertSee('Existe en el sistema')
            ->assertSee('Bebidas viejas')
            ->assertSee('Valor anterior', false);
    }

    public function test_show_marks_deleted_records(): void
    {
        $this->signIn();

        $log = new AuditLog([
            'action' => 'DELETED',
            'affected_table' => 'categories',
            'record_id' => 9999,
            'old_values' => ['name' => 'Categoría eliminada'],
        ]);
        $log->created_at = Carbon::parse('2026-08-20 10:00:00');
        $log->save();

        $this->get(route('audit.show', $log))
            ->assertOk()
            ->assertSee('Categoría eliminada')
            ->assertSee('Eliminado o no encontrado');
    }
}
