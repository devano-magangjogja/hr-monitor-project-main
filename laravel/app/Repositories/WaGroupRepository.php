<?php

namespace App\Repositories;

use App\Models\WaGroup;
use Illuminate\Database\Eloquent\Collection;

class WaGroupRepository
{
    public function all(): Collection
    {
        return WaGroup::orderBy('id')->get();
    }

    public function active(): Collection
    {
        return WaGroup::active()->get();
    }

    public function create(array $data): WaGroup
    {
        return WaGroup::create($data);
    }

    public function update(WaGroup $group, array $data): bool
    {
        return $group->update($data);
    }

    public function delete(WaGroup $group): bool
    {
        return $group->delete();
    }
}
