<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    public function __construct(protected User $model) {}

    public function findActiveByEmail(string $email): ?User
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->where('email', $email)
            ->where('is_active', 1)
            ->first();
    }

    public function getAllExceptAdmin(): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->whereIn('role', ['hr_staff', 'hr_assistant', 'cs', 'ob'])
            ->orderBy('role')
            ->orderBy('name')
            ->get();
    }

    public function getAllByRole(string $role): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->where('role', $role)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
    }

    public function findById(int $id): ?User
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query->find($id);
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    public function updatePassword(User $user, string $hashedPassword): bool
    {
        return $user->update(['password' => $hashedPassword]);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function isEmailTaken(string $email, ?int $exceptId = null): bool
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        $query->where('email', $email);

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }

    public function updateProfile(User $user, array $data): bool
    {
        return $user->update($data);
    }
}