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
            ->map(function ($workflow) {            // Extraire la première section de la description (jusqu'au premier double saut de ligne)
            $description = explode("\n\n", $workflow->description)[0];
            
            return [
                'id' => $workflow->id,
                'slug' => $workflow->slug,
                'title' => $workflow->title,
                'description' => $description,
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
