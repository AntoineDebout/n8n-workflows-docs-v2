<?php

namespace App\Presenters\Workflow;

use App\Models\Workflow;
use Illuminate\Pagination\LengthAwarePaginator;

class WorkflowPresenter
{
    public function presentSingle(Workflow $workflow, ?int $currentUserId = null): array
    {
        return WorkflowViewModel::fromModel($workflow, $currentUserId)->toArray();
    }

    public function presentList(
        LengthAwarePaginator $paginator, 
        array $availableTags = [], 
        array $filters = [],
        ?int $currentUserId = null
    ): array {
        return WorkflowListViewModel::fromPaginator(
            $paginator, 
            $availableTags, 
            $filters, 
            $currentUserId
        )->toArray();
    }

    public function presentForCreation(array $availableTags = []): array
    {
        return [
            'availableTags' => $availableTags,
            'visibilityOptions' => [
                'private' => 'Privé',
                'team' => 'Équipe',
                'public' => 'Public'
            ]
        ];
    }

    public function presentForEdit(Workflow $workflow, array $availableTags = []): array
    {
        return [
            'workflow' => $this->presentSingle($workflow),
            'availableTags' => $availableTags,
            'visibilityOptions' => [
                'private' => 'Privé',
                'team' => 'Équipe',
                'public' => 'Public'
            ]
        ];
    }
}