<?php

namespace App\UseCases\Workflow\Create;

class CreateWorkflowOutput
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $workflowSlug = null,
        public readonly ?string $message = null,
        public readonly array $errors = []
    ) {}

    public static function success(string $workflowSlug): self
    {
        return new self(
            success: true,
            workflowSlug: $workflowSlug,
            message: 'Workflow créé avec succès'
        );
    }

    public static function failure(string $message, array $errors = []): self
    {
        return new self(
            success: false,
            message: $message,
            errors: $errors
        );
    }
}