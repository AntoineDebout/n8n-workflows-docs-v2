<?php

namespace App\Presenters\Workflow;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Workflow;

class WorkflowListViewModel
{
    public function __construct(
        public readonly array $workflows,
        public readonly array $pagination,
        public readonly array $availableTags,
        public readonly array $filters
    ) {}

    public static function fromPaginator(
        LengthAwarePaginator $paginator, 
        array $availableTags = [], 
        array $filters = [],
        ?int $currentUserId = null
    ): self {
        $workflows = $paginator->getCollection()->map(function (Workflow $workflow) use ($currentUserId) {
            return WorkflowViewModel::fromModel($workflow, $currentUserId)->toArray();
        })->toArray();

        return new self(
            workflows: $workflows,
            pagination: [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'has_more' => $paginator->hasMorePages(),
            ],
            availableTags: $availableTags,
            filters: $filters
        );
    }

    public function toArray(): array
    {
        return [
            'workflows' => $this->workflows,
            'pagination' => $this->pagination,
            'availableTags' => $this->availableTags,
            'filters' => $this->filters,
        ];
    }
}