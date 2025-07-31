<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateWorkflowRequest extends FormRequest
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
        ];
    }
}