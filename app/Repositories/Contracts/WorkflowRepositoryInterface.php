<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\Workflow;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface WorkflowRepositoryInterface
{
    public function findById(int $id): ?Workflow;
    
    public function findBySlug(string $slug): ?Workflow;
    
    public function create(array $data): Workflow;
    
    public function update(Workflow $workflow, array $data): bool;
    
    public function delete(Workflow $workflow): bool;
    
    public function getAllVisible(?User $user = null, array $filters = [], string $sortBy = 'created_at', string $sortDirection = 'desc'): LengthAwarePaginator;
    
    public function getByUser(User $user, array $filters = []): Collection;
    
    public function getAllTags(): array;
}