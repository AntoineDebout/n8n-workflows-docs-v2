<?php
namespace App\UseCases\Workflow\GetList;

use App\Models\User;
use App\Repositories\Contracts\WorkflowRepositoryInterface;
use Exception;

class GetWorkflowListAction
{
    public function __construct(
        private readonly WorkflowRepositoryInterface $workflowRepository
    ) {}

    public function execute(GetWorkflowListInput $input): GetWorkflowListOutput
    {
        try {
            $user = $input->userId ? User::find($input->userId) : null;
            
            $workflows = $this->workflowRepository->getAllVisible(
                $user,
                $input->filters,
                $input->sortBy,
                $input->sortDirection
            );
            
            return GetWorkflowListOutput::success($workflows);
        } catch (Exception $e) {
            return GetWorkflowListOutput::failure('Erreur lors de la récupération des workflows');
        }
    }
}