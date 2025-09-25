<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryComplementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => [
                'required',
                'string',
                'max:190',
                Rule::unique('category_complements', 'name')->whereNull('deleted_at')->ignore($this->route('categoryComplement.id')),
            ],
            'description' => ['nullable', 'string', 'max:900'],
        ];
    }
}