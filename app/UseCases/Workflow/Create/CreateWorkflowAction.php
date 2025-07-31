<?php
namespace App\UseCases\Workflow\Create;

use App\Repositories\Contracts\WorkflowRepositoryInterface;
use Exception;

class CreateWorkflowAction
{
    public function __construct(
        private readonly WorkflowRepositoryInterface $workflowRepository
    ) {}

    public function execute(CreateWorkflowInput $input): CreateWorkflowOutput
    {
        try {
            $workflow = $this->workflowRepository->create($input->toArray());
            
            return CreateWorkflowOutput::success($workflow->slug);
        } catch (Exception $e) {
            return CreateWorkflowOutput::failure(
                'Erreur lors de la création du workflow',
                ['general' => $e->getMessage()]
            );
        }
    }
}