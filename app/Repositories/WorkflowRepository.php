<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Workflow;
use App\Repositories\Contracts\WorkflowRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class WorkflowRepository implements WorkflowRepositoryInterface
{
    public function findById(int $id): ?Workflow
    {
        return Workflow::with('user')->find($id);
    } 

    public function findBySlug(string $slug): ?Workflow
    {
        return Workflow::with('user')->where('slug', $slug)->first();
    }

    public function create(array $data): Workflow
    {
        return Workflow::create($data);
    }

    public function update(Workflow $workflow, array $data): bool
    {
        return $workflow->update($data);
    }

    public function delete(Workflow $workflow): bool
    {
        return $workflow->delete();
    }

    public function getAllVisible(
        ?User $user = null, 
        array $filters = [], 
        string $sortBy = 'created_at', 
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        $query = Workflow::with('user')
            ->visible($user)
            ->withFilters($filters)
            ->orderBy($sortBy, $sortDirection);

        return $query->paginate(12);
    }

    public function getByUser(User $user, array $filters = []): Collection
    {
        return Workflow::with('user')
            ->where('user_id', $user->id)
            ->withFilters($filters)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAllTags(): array
    {
        $workflows = Workflow::whereNotNull('tags')->get(['tags']);
        $allTags = [];

        foreach ($workflows as $workflow) {
            if ($workflow->tags) {
                $allTags = array_merge($allTags, $workflow->tags);
            }
        }

        return array_values(array_unique($allTags));
    }
}