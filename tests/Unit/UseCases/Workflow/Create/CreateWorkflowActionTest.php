<?php

namespace Tests\Unit\UseCases\Workflow\Create;

use Tests\TestCase;
use App\UseCases\Workflow\Create\CreateWorkflowAction;
use App\UseCases\Workflow\Create\CreateWorkflowInput;
use App\UseCases\Workflow\Create\CreateWorkflowOutput;
use App\Repositories\Contracts\WorkflowRepositoryInterface;
use App\Models\Workflow;
use Mockery;


class CreateWorkflowActionTest extends TestCase
{
    private WorkflowRepositoryInterface $repository;
    private CreateWorkflowAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(WorkflowRepositoryInterface::class);
        $this->action = new CreateWorkflowAction($this->repository);
    }

    public function test_it_creates_workflow_successfully()
    {
        // Arrange
        $input = new CreateWorkflowInput(
            title: 'Test Workflow',
            description: 'Test Description',
            workflowJson: ['nodes' => [], 'connections' => []],
            tags: ['test', 'automation'],
            visibility: 'public',
            userId: 1
        );

        $expectedWorkflow = new Workflow([
            'title' => $input->title,
            'description' => $input->description,
            'workflow_json' => $input->workflowJson,
            'tags' => $input->tags,
            'visibility' => $input->visibility,
            'user_id' => $input->userId,
            'slug' => 'test-workflow'
        ]);

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with($input->toArray())
            ->andReturn($expectedWorkflow);

        // Act
        $output = $this->action->execute($input);

        // Assert
        $this->assertTrue($output->success);
        $this->assertEquals('test-workflow', $output->workflowSlug);
        $this->assertEmpty($output->errors);
    }

    public function test_it_handles_creation_failure()
    {
        // Arrange
        $input = new CreateWorkflowInput(
            title: 'Test Workflow',
            description: 'Test Description',
            workflowJson: ['nodes' => [], 'connections' => []],
            tags: ['test'],
            visibility: 'public',
            userId: 1
        );

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with($input->toArray())
            ->andThrow(new \Exception('Database error'));

        // Act
        $output = $this->action->execute($input);

        // Assert
        $this->assertFalse($output->success);
        $this->assertEquals('Erreur lors de la création du workflow', $output->message);
        $this->assertEquals(['general' => 'Database error'], $output->errors);
    }
}
