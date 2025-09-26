<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ComplementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $complement = $this->route('complement');
        $categoriesRule = $this->isMethod('post') ? ['required', 'array', 'min:1'] : ['sometimes', 'array', 'min:1'];

        return [
            'name'        => [
                'required',
                'string',
                'max:190',
                Rule::unique('complements', 'name')->whereNull('deleted_at')->ignore(optional($complement)->id),
            ],
            'description' => ['nullable', 'string', 'max:900'],
            'price'       => ['required', 'numeric', 'min:0'],
            'status'      => ['required', 'integer'],
            'categories'  => $categoriesRule,
            'categories.*'=> ['integer', 'distinct', 'exists:category_complements,id'],
        ];
    }
}