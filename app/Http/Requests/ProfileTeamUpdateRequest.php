<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileTeamUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'team_id' => ['nullable', 'exists:teams,id'],
        ];
    }
}
