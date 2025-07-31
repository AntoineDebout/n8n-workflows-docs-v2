<?php
namespace App\UseCases\Workflow\Create;

class CreateWorkflowInput
{
    public function __construct(
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
            'user_id' => $this->userId,
        ];
    }
}