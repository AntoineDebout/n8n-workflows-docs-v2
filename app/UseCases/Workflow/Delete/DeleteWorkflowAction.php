<?php
namespace App\UseCases\Workflow\Delete;

use App\Repositories\Contracts\WorkflowRepositoryInterface;
use Exception;

class DeleteWorkflowAction
{
    public function __construct(
        private readonly WorkflowRepositoryInterface $workflowRepository
    ) {}

    public function execute(string $slug, int $userId): bool
    {
        try {
            $workflow = $this->workflowRepository->findBySlug($slug);
            
            if (!$workflow || $workflow->user_id !== $userId) {
                return false;
            }

            return $this->workflowRepository->delete($workflow);
        } catch (Exception $e) {
            return false;
        }
    }
}