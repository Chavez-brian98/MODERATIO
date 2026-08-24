<?php

namespace App\Observers;

use App\Services\AuditService;
use Illuminate\Database\Eloquent\Model;

class AuditableObserver
{
    protected array $masked = ['password', 'remember_token'];

    protected array $timestamps = ['created_at', 'updated_at'];

    public function created(Model $model): void
    {
        AuditService::log('CREATED', $model->getTable(), $model->getKey(), $this->snapshot($model));
    }

    public function updated(Model $model): void
    {
        $before = [];
        $after = [];

        foreach ($model->getChanges() as $key => $value) {
            if (in_array($key, $this->timestamps, true)) {
                continue;
            }

            $before[$key] = $this->mask($model, $key, $model->getOriginal($key));
            $after[$key] = $this->mask($model, $key, $value);
        }

        AuditService::log('UPDATED', $model->getTable(), $model->getKey(), [
            'before' => $before,
            'after' => $after,
        ]);
    }

    public function deleted(Model $model): void
    {
        AuditService::log('DELETED', $model->getTable(), $model->getKey(), $this->snapshot($model));
    }

    protected function snapshot(Model $model): array
    {
        $attributes = $model->toArray();

        unset($attributes['created_at'], $attributes['updated_at']);

        return collect($attributes)
            ->map(fn ($value, $key) => $this->mask($model, $key, $value))
            ->all();
    }

    protected function mask(Model $model, string $key, mixed $value): mixed
    {
        if (in_array($key, $this->sensitiveKeys($model), true)) {
            return '[oculto]';
        }

        return $value;
    }

    protected function sensitiveKeys(Model $model): array
    {
        return array_values(array_unique(array_merge($this->masked, $model->getHidden())));
    }
}
