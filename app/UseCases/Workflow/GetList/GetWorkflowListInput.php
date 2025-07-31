<?php

namespace App\UseCases\Workflow\GetList;

class GetWorkflowListInput
{
    public function __construct(
        public readonly ?int $userId = null,
        public readonly array $filters = [],
        public readonly string $sortBy = 'created_at',
        public readonly string $sortDirection = 'desc'
    ) {}
}
