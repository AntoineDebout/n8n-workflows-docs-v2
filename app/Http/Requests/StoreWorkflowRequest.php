<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreWorkflowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',
            'workflow_json' => 'required|array',
            'workflow_json.nodes' => 'required|array|min:1',
            'workflow_json.connections' => 'present|array',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'visibility' => 'required|in:private,team,public',
            'json_file' => 'nullable|file|mimes:json|max:2048', // Pour l'upload optionnel
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'workflow_json.required' => 'Le JSON du workflow est obligatoire.',
            'workflow_json.array' => 'Le format du workflow n\'est pas valide.',
            'workflow_json.nodes.required' => 'Le workflow doit contenir au moins un nœud.',
            'workflow_json.nodes.min' => 'Le workflow doit contenir au moins un nœud.',
            'tags.array' => 'Les tags doivent être un tableau.',
            'tags.*.string' => 'Chaque tag doit être une chaîne de caractères.',
            'tags.*.max' => 'Chaque tag ne peut pas dépasser 50 caractères.',
            'visibility.required' => 'La visibilité est obligatoire.',
            'visibility.in' => 'La visibilité doit être privée, équipe ou publique.',
            'json_file.file' => 'Le fichier doit être un fichier valide.',
            'json_file.mimes' => 'Le fichier doit être au format JSON.',
            'json_file.max' => 'Le fichier ne peut pas dépasser 2MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Si un fichier JSON est uploadé, on le parse et on l'ajoute aux données
        if ($this->hasFile('json_file')) {
            $file = $this->file('json_file');
            $content = file_get_contents($file->getRealPath());
            $jsonData = json_decode($content, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->merge(['workflow_json' => $jsonData]);
            }
        }
    }
}