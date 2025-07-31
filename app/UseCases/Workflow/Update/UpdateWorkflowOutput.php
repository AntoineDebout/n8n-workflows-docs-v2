<?php 

namespace App\UseCases\Workflow\Update;

class UpdateWorkflowOutput
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $message = null,
        public readonly array $errors = []
    ) {}

    public static function success(): self
    {
        return new self(
            success: true,
            message: 'Workflow mis à jour avec succès'
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