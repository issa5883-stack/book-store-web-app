<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation shared by users (book requests) and admins (direct uploads).
 * The PDF file is required when creating and optional when updating.
 */
class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isCreating = $this->isMethod('post');

        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'language' => ['nullable', 'string', 'max:50'],
            'published_year' => ['nullable', 'integer', 'min:1000', 'max:'.(date('Y') + 1)],
            'pages' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'pdf' => [$isCreating ? 'required' : 'nullable', 'file', 'mimes:pdf', 'max:51200'],
        ];
    }
}
