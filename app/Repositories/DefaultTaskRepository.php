<?php

namespace App\Repositories;

use App\Models\DefaultTask;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class DefaultTaskRepository
{
    public function __construct(protected DefaultTask $model) {}

    public function getAll(): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->with('creator:id,name')
            ->orderBy('target_role')
            ->orderBy('title')
            ->get();
    }

    public function findById(int $id): ?DefaultTask
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query->find($id);
    }

    public function getActiveByRole(string $role): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->where('target_role', $role)
            ->where('is_active', 1)
            ->get();
    }

    public function getAllActive(): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->with('creator:id,name')
            ->where('is_active', 1)
            ->get();
    }

    public function create(array $data): DefaultTask
    {
        return $this->model->create($data);
    }

    public function update(DefaultTask $defaultTask, array $data): bool
    {
        return $defaultTask->update($data);
    }

    public function delete(DefaultTask $defaultTask): bool
    {
        return $defaultTask->delete();
    }
}