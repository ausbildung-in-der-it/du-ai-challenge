<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNewsletterLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc', 'max:255'],
            'linkedin_url' => ['nullable', 'string', 'max:255'],
            'privacy_accepted' => ['accepted'],
            'source' => ['required', Rule::in(['homepage', 'challenge_complete'])],
            'journey_slug' => ['nullable', 'string', 'max:100'],
            'journey_session_nanoid' => ['nullable', 'string', 'size:21'],
            'completed_challenge' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => str($this->string('email')->toString())->trim()->lower()->toString(),
            'linkedin_url' => $this->filled('linkedin_url')
                ? str($this->string('linkedin_url')->toString())->trim()->toString()
                : null,
            'privacy_accepted' => $this->boolean('privacy_accepted'),
            'journey_slug' => $this->filled('journey_slug')
                ? str($this->string('journey_slug')->toString())->trim()->toString()
                : null,
            'journey_session_nanoid' => $this->filled('journey_session_nanoid')
                ? str($this->string('journey_session_nanoid')->toString())->trim()->toString()
                : null,
            'completed_challenge' => $this->boolean('completed_challenge'),
        ]);
    }
}
