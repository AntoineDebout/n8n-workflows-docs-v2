<?php

namespace App\UseCases\Workflow\GetList;

class GetWorkflowListOutput
{
    public function __construct(
        public readonly bool $success,
        public readonly mixed $workflows = null,
        public readonly ?string $message = null
    ) {}

    public static function success($workflows): self
    {
        return new self(
            success: true,
            workflows: $workflows
        );
    }

    public static function failure(string $message): self
    {
        return new self(
            success: false,
            message: $message
        );
    }
}