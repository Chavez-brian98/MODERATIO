<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Observers\AuditableObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected array $auditableModels = [
        Category::class,
        Customer::class,
        Product::class,
        Role::class,
        User::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach ($this->auditableModels as $model) {
            $model::observe(AuditableObserver::class);
        }

        Gate::before(function ($user, string $ability) {
            if ($user instanceof User && $user->hasEffectivePermission($ability)) {
                return true;
            }

            return null;
        });
    }
}
