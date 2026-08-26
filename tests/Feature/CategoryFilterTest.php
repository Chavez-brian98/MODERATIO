<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryFilterTest extends TestCase
{
    use RefreshDatabase;

    private function seedCategories(): void
    {
        $parent = Category::create(['name' => 'Bebidas', 'is_active' => true]);

        Category::create([
            'name' => 'Gaseosas',
            'parent_category_id' => $parent->id,
            'is_active' => true,
        ]);

        Category::create(['name' => 'Snacks', 'is_active' => false]);
    }

    public function test_index_shows_all_categories_by_default(): void
    {
        $this->seedCategories();
        $this->signIn();

        $this->get(route('categories.index'))
            ->assertOk()
            ->assertViewHas('categories', fn ($paginator) => $paginator->total() === 3)
            ->assertSee('Bebidas')
            ->assertSee('Gaseosas')
            ->assertSee('Snacks');
    }

    public function test_index_can_filter_parent_categories_only(): void
    {
        $this->seedCategories();
        $this->signIn();

        $this->get(route('categories.index', ['type' => 'parent']))
            ->assertOk()
            ->assertViewHas('categories', fn ($paginator) => $paginator->total() === 2)
            ->assertSee('Bebidas')
            ->assertDontSee('Gaseosas');
    }

    public function test_index_can_filter_subcategories_only(): void
    {
        $this->seedCategories();
        $this->signIn();

        $this->get(route('categories.index', ['type' => 'sub']))
            ->assertOk()
            ->assertViewHas('categories', fn ($paginator) => $paginator->total() === 1)
            ->assertSee('Gaseosas');
    }

    public function test_index_can_filter_by_active_status(): void
    {
        $this->seedCategories();
        $this->signIn();

        $this->get(route('categories.index', ['status' => 'active']))
            ->assertOk()
            ->assertViewHas('categories', fn ($paginator) => $paginator->total() === 2)
            ->assertSee('Bebidas')
            ->assertDontSee('Snacks');
    }

    public function test_index_can_filter_by_inactive_status(): void
    {
        $this->seedCategories();
        $this->signIn();

        $this->get(route('categories.index', ['status' => 'inactive']))
            ->assertOk()
            ->assertViewHas('categories', fn ($paginator) => $paginator->total() === 1)
            ->assertSee('Snacks')
            ->assertDontSee('Bebidas');
    }

    public function test_index_combines_type_and_status_filters(): void
    {
        $this->seedCategories();
        $parent = Category::where('name', 'Bebidas')->first();

        Category::create([
            'name' => 'Colas',
            'parent_category_id' => $parent->id,
            'is_active' => false,
        ]);

        $user = $this->signIn();

        $this->actingAs($user)
            ->get(route('categories.index', ['type' => 'sub', 'status' => 'inactive']))
            ->assertOk()
            ->assertViewHas('categories', fn ($paginator) => $paginator->total() === 1)
            ->assertSee('Colas')
            ->assertDontSee('Gaseosas');
    }

    public function test_invalid_filter_values_fall_back_to_showing_everything(): void
    {
        $this->seedCategories();
        $this->signIn();

        $this->get(route('categories.index', ['type' => 'bogus', 'status' => 'bogus']))
            ->assertOk()
            ->assertViewHas('categories', fn ($paginator) => $paginator->total() === 3)
            ->assertSee('Bebidas')
            ->assertSee('Gaseosas')
            ->assertSee('Snacks');
    }
}
