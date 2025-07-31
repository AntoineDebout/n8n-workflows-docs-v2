<?php

namespace App\UseCases\Workflow\Update;

class UpdateWorkflowInput
{
    public function __construct(
        public readonly string $slug,
        public readonly string $title,
        public readonly ?string $description,
        public readonly array $workflowJson,
        public readonly array $tags,
        public readonly string $visibility,
        public readonly int $userId
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'workflow_json' => $this->workflowJson,
            'tags' => $this->tags,
            'visibility' => $this->visibility,
        ];
    }
}