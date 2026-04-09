<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePuzzleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'initial_fen' => 'required|string',
            'solution' => 'required|array',
            'solution.*' => 'required|string',
            'difficulty' => 'required|in:easy,medium,hard',
        ];
    }
}

