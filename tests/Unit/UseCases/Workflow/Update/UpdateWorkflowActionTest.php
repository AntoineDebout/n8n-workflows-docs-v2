<?php

namespace Tests\Unit\UseCases\Workflow\Update;

use Tests\TestCase;
use App\UseCases\Workflow\Update\UpdateWorkflowAction;
use App\UseCases\Workflow\Update\UpdateWorkflowInput;
use App\UseCases\Workflow\Update\UpdateWorkflowOutput;
use App\Repositories\Contracts\WorkflowRepositoryInterface;
use App\Models\Workflow;
use Mockery;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class UpdateWorkflowActionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var WorkflowRepositoryInterface&\Mockery\MockInterface */
    private $repository;
    private UpdateWorkflowAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(WorkflowRepositoryInterface::class);
        $this->action = new UpdateWorkflowAction($this->repository);
    }

    public function test_it_updates_workflow_successfully()
    {
        // Arrange
        $input = new UpdateWorkflowInput(
            slug: 'test-workflow',
            userId: 1,
            title: 'Updated Workflow',
            description: 'Updated Description',
            workflowJson: ['nodes' => ['1'], 'connections' => []],
            tags: ['updated'],
            visibility: 'private'
        );

        $existingWorkflow = new Workflow([
            'id' => 1,
            'title' => 'Original Title',
            'slug' => $input->slug,
            'user_id' => $input->userId
        ]);

        $updatedWorkflow = new Workflow([
            'id' => 1,
            'title' => $input->title,
            'description' => $input->description,
            'workflow_json' => $input->workflowJson,
            'tags' => $input->tags,
            'visibility' => $input->visibility,
            'slug' => 'updated-workflow'
        ]);

        $this->repository
            ->shouldReceive('findBySlug')
            ->once()
            ->with($input->slug)
            ->andReturn($existingWorkflow);

        $this->repository
            ->shouldReceive('update')
            ->once()
            ->with($existingWorkflow, $input->toArray())
            ->andReturn(true);

        // Act
        $output = $this->action->execute($input);

        // Assert
        $this->assertTrue($output->success);
        $this->assertEquals('Workflow mis à jour avec succès', $output->message);
    }

    public function test_it_handles_nonexistent_workflow()
    {
        // Arrange
        $input = new UpdateWorkflowInput(
            slug: 'nonexistent-workflow',
            userId: 1,
            title: 'Updated Workflow',
            description: 'Updated Description',
            workflowJson: ['nodes' => []],
            tags: ['updated'],
            visibility: 'private'
        );

        $this->repository
            ->shouldReceive('findBySlug')
            ->once()
            ->with($input->slug)
            ->andReturnNull();

        // Act
        $output = $this->action->execute($input);

        // Assert
        $this->assertFalse($output->success);
        $this->assertEquals('Workflow non trouvé', $output->message);
    }

    public function test_it_handles_unauthorized_update()
    {
        // Arrange
        $input = new UpdateWorkflowInput(
            slug: 'test-workflow',
            userId: 1,
            title: 'Updated Workflow',
            description: 'Updated Description',
            workflowJson: ['nodes' => []],
            tags: ['updated'],
            visibility: 'private'
        );

        $existingWorkflow = new Workflow([
            'id' => 1,
            'title' => 'Original Title',
            'slug' => $input->slug,
            'user_id' => 2 // Different user
        ]);

        $this->repository
            ->shouldReceive('findBySlug')
            ->once()
            ->with($input->slug)
            ->andReturn($existingWorkflow);

        // Act
        $output = $this->action->execute($input);

        // Assert
        $this->assertFalse($output->success);
        $this->assertEquals('Non autorisé à modifier ce workflow', $output->message);
    }

    public function test_it_handles_update_failure()
    {
        // Arrange
        $input = new UpdateWorkflowInput(
            slug: 'test-workflow',
            userId: 1,
            title: 'Updated Workflow',
            description: 'Updated Description',
            workflowJson: ['nodes' => []],
            tags: ['updated'],
            visibility: 'private'
        );

        $existingWorkflow = new Workflow([
            'id' => 1,
            'title' => 'Original Title',
            'slug' => $input->slug,
            'user_id' => $input->userId
        ]);

        $this->repository
            ->shouldReceive('findBySlug')
            ->once()
            ->with($input->slug)
            ->andReturn($existingWorkflow);

        $this->repository
            ->shouldReceive('update')
            ->once()
            ->andThrow(new \Exception('Database error'));

        // Act
        $output = $this->action->execute($input);

        // Assert
        $this->assertFalse($output->success);
        $this->assertEquals('Erreur lors de la mise à jour du workflow', $output->message);
        $this->assertEquals(['general' => 'Database error'], $output->errors);
    }
}
