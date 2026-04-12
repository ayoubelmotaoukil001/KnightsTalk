<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePuzzleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            && ($this->user()->is_admin || app()->environment('local'));
    }

    protected function prepareForValidation(): void
    {
        if (! is_array($this->solution)) {
            return;
        }

        $clean = [];
        foreach ($this->solution as $move) {
            if (is_string($move) && trim($move) !== '') {
                $clean[] = trim($move);
            }
        }

        $this->merge(['solution' => $clean]);
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'initial_fen' => 'required|string',
            'solution' => 'required|array|min:1',
            'solution.*' => 'required|string',
            'difficulty' => 'required|in:easy,medium,hard',
        ];
    }
}
