<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $workflows = Workflow::where('visibility', 'public')
            ->with('user')
            ->latest()
            ->get()
            ->map(function ($workflow) {
                return [
                    'id' => $workflow->id,
                    'slug' => $workflow->slug,
                    'title' => $workflow->title,
                    'description' => $workflow->description,
                    'tags' => $workflow->tags,
                    'author' => $workflow->user->name,
                    'created_at' => $workflow->created_at->format('d/m/Y'),
                ];
            });

        return Inertia::render('Home', [
            'workflows' => $workflows
        ]);
    }
}
