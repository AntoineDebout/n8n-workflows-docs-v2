<?php

namespace App\Presenters\Workflow;

use App\Models\Workflow;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WorkflowViewModel
{
    public function __construct(
        public readonly string $slug,
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $descriptionExcerpt,
        public readonly array $workflowJson,
        public readonly array $tags,
        public readonly string $visibility,
        public readonly string $authorName,
        public readonly string $createdAt,
        public readonly string $updatedAt,
        public readonly bool $canEdit = false,
        public readonly bool $canDelete = false
    ) {}

    public static function fromModel(Workflow $workflow, ?int $currentUserId = null): self
    {
        $canEdit = $currentUserId && $workflow->user_id === $currentUserId;
        $canDelete = $canEdit;

        return new self(
            slug: $workflow->slug,
            title: $workflow->title,
            description: $workflow->description,
            descriptionExcerpt: $workflow->getDescriptionExcerpt(),
            workflowJson: $workflow->workflow_json,
            tags: $workflow->tags ?? [],
            visibility: $workflow->visibility,
            authorName: $workflow->user->name,
            createdAt: $workflow->created_at->format('d/m/Y H:i'),
            updatedAt: $workflow->updated_at->format('d/m/Y H:i'),
            canEdit: $canEdit,
            canDelete: $canDelete
        );
    }

    public function toArray(): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'descriptionExcerpt' => $this->descriptionExcerpt,
            'workflowJson' => $this->workflowJson,
            'tags' => $this->tags,
            'visibility' => $this->visibility,
            'authorName' => $this->authorName,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'canEdit' => $this->canEdit,
            'canDelete' => $this->canDelete,
        ];
    }
}