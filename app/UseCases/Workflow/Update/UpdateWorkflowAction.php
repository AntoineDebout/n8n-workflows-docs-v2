<?php 
namespace App\UseCases\Workflow\Update;

use App\Repositories\Contracts\WorkflowRepositoryInterface;
use Exception;

class UpdateWorkflowAction
{
    public function __construct(
        private readonly WorkflowRepositoryInterface $workflowRepository
    ) {}

    public function execute(UpdateWorkflowInput $input): UpdateWorkflowOutput
    {
        try {
            $workflow = $this->workflowRepository->findBySlug($input->slug);
            
            if (!$workflow) {
                return UpdateWorkflowOutput::failure('Workflow non trouvé');
            }

            if ($workflow->user_id !== $input->userId) {
                return UpdateWorkflowOutput::failure('Non autorisé à modifier ce workflow');
            }

            $this->workflowRepository->update($workflow, $input->toArray());
            
            return UpdateWorkflowOutput::success();
        } catch (Exception $e) {
            return UpdateWorkflowOutput::failure(
                'Erreur lors de la mise à jour du workflow',
                ['general' => $e->getMessage()]
            );
        }
    }
}