<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class OfferItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [

            'item_id'   => [
                'required',
                'numeric',
                'exists:items,id',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1', // 👈 no permite cantidades 0 o negativas
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => 'The item field is required.',
            'item_id.exists'   => 'The selected item does not exist.',
            'quantity.required' => 'The quantity field is required.',
            'quantity.integer'  => 'The quantity must be an integer.',
            'quantity.min'      => 'The quantity must be at least 1.',
        ];
    }
}