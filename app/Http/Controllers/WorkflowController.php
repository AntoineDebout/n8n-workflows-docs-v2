<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkflowRequest;
use App\Http\Requests\UpdateWorkflowRequest;
use App\Models\Workflow;
use App\Presenters\Workflow\WorkflowPresenter;
use App\Repositories\Contracts\WorkflowRepositoryInterface;
use App\UseCases\Workflow\Create\CreateWorkflowAction;
use App\UseCases\Workflow\Create\CreateWorkflowInput;
use App\UseCases\Workflow\Delete\DeleteWorkflowAction;
use App\UseCases\Workflow\GetList\GetWorkflowListAction;
use App\UseCases\Workflow\GetList\GetWorkflowListInput;
use App\UseCases\Workflow\Update\UpdateWorkflowAction;
use App\UseCases\Workflow\Update\UpdateWorkflowInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WorkflowController extends Controller
{
    public function __construct(
        private readonly GetWorkflowListAction $getWorkflowListAction,
        private readonly CreateWorkflowAction $createWorkflowAction,
        private readonly UpdateWorkflowAction $updateWorkflowAction,
        private readonly DeleteWorkflowAction $deleteWorkflowAction,
        private readonly WorkflowRepositoryInterface $workflowRepository,
        private readonly WorkflowPresenter $presenter
    ) {}

    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'tags', 'author']);
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        $input = new GetWorkflowListInput(
            userId: auth()->id(),
            filters: $filters,
            sortBy: $sortBy,
            sortDirection: $sortDirection
        );

        $output = $this->getWorkflowListAction->execute($input);

        if (!$output->success) {
            return Inertia::render('Workflows/Index', [
                'error' => $output->message,
                'workflows' => [],
                'pagination' => [],
                'availableTags' => [],
                'filters' => $filters
            ]);
        }

        $availableTags = $this->workflowRepository->getAllTags();
        
        $presentedData = $this->presenter->presentList(
            $output->workflows,
            $availableTags,
            $filters,
            auth()->id()
        );

        return Inertia::render('Workflows/Index', $presentedData);
    }

    public function create(): Response
    {
        $availableTags = $this->workflowRepository->getAllTags();
        $data = $this->presenter->presentForCreation($availableTags);

        return Inertia::render('Workflows/Create', $data);
    }

    public function store(StoreWorkflowRequest $request): RedirectResponse
    {
        $input = new CreateWorkflowInput(
            title: $request->validated('title'),
            description: $request->validated('description'),
            workflowJson: $request->validated('workflow_json'),
            tags: $request->validated('tags', []),
            visibility: $request->validated('visibility', 'private'),
            userId: auth()->id()
        );

        $output = $this->createWorkflowAction->execute($input);

        if (!$output->success) {
            return back()->withErrors($output->errors)->with('error', $output->message);
        }

        return redirect()->route('workflows.show', $output->workflowSlug)
            ->with('success', $output->message);
    }

    public function show(string $slug): Response
    {
        $workflow = $this->workflowRepository->findBySlug($slug);

        if (!$workflow) {
            abort(404, 'Workflow non trouvé');
        }

        // Vérifier la visibilité
        if ($workflow->visibility === 'private' && $workflow->user_id !== auth()->id()) {
            abort(403, 'Accès non autorisé');
        }

        $data = $this->presenter->presentSingle($workflow, auth()->id());

        return Inertia::render('Workflows/Show', [
            'workflow' => $data
        ]);
    }

    public function showPublic(string $slug): Response
    {
        $workflow = $this->workflowRepository->findBySlug($slug);

        if (!$workflow || $workflow->visibility !== 'public') {
            abort(404, 'Workflow non trouvé');
        }

        $data = $this->presenter->presentSingle($workflow, null);

        return Inertia::render('Workflows/Show', [
            'workflow' => $data,
            'isPublicView' => true
        ]);
    }

    public function edit(string $slug): Response
    {
        $workflow = $this->workflowRepository->findBySlug($slug);

        if (!$workflow || $workflow->user_id !== auth()->id()) {
            abort(404, 'Workflow non trouvé ou non autorisé');
        }

        $availableTags = $this->workflowRepository->getAllTags();
        $data = $this->presenter->presentForEdit($workflow, $availableTags);

        return Inertia::render('Workflows/Edit', $data);
    }

    public function update(UpdateWorkflowRequest $request, string $slug): RedirectResponse
    {
        $input = new UpdateWorkflowInput(
            slug: $slug,
            title: $request->validated('title'),
            description: $request->validated('description'),
            workflowJson: $request->validated('workflow_json'),
            tags: $request->validated('tags', []),
            visibility: $request->validated('visibility', 'private'),
            userId: auth()->id()
        );

        $output = $this->updateWorkflowAction->execute($input);

        if (!$output->success) {
            return back()->withErrors($output->errors)->with('error', $output->message);
        }

        return redirect()->route('workflows.show', $slug)
            ->with('success', $output->message);
    }

    public function destroy(string $slug): RedirectResponse
    {
        $success = $this->deleteWorkflowAction->execute($slug, auth()->id());

        if (!$success) {
            return back()->with('error', 'Erreur lors de la suppression du workflow');
        }

        return redirect()->route('workflows.index')
            ->with('success', 'Workflow supprimé avec succès');
    }
}