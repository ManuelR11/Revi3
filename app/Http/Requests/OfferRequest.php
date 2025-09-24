<?php

namespace App\Http\Requests;

use App\Rules\IniAmount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\OfferType; // ajusta el namespace si es necesario

class OfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normaliza entradas antes de validar
     */
    protected function prepareForValidation(): void
    {
        // Asegura que 'type' llegue en un formato consistente (int)
        $type = $this->input('type');

        // Si es un enum/backed enum puedes mapear así:
        if ($type instanceof \BackedEnum) {
            $type = $type->value;
        } elseif (is_string($type) && defined(OfferType::class.'::'.$type)) {
            // Si te llega 'DISCOUNT' o 'COMBO' como string y tienes constantes, convier­te­lo
            $type = constant(OfferType::class.'::'.$type);
        }

        $this->merge([
            'type' => is_numeric($type) ? (int) $type : $type,
            // Normaliza vacíos a null para evitar "a veces pasa":
            'amount' => $this->filled('amount') ? $this->input('amount') : null,
            'combo_price' => $this->filled('combo_price') ? $this->input('combo_price') : null,
        ]);
    }

    public function rules(): array
    {
        // Obtén el ID de la ruta sin romper si es binding por modelo o por parámetro
        $routeOffer = $this->route('offer');
        $offerId = is_object($routeOffer) ? ($routeOffer->id ?? null) : $this->route('offer.id');

        return [
            'name'        => [
                'required', 'string', 'max:190',
                Rule::unique('offers', 'name')->ignore($offerId),
            ],
            // Campos compartidos
            'status'     => ['required', 'numeric', 'max:24'],
            'start_date' => ['required', 'string', 'max:190'],
            'end_date'   => ['required', 'string', 'max:190'],
            'image'      => $offerId
                ? ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048']
                : ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],

            // Tipo (gatilla las reglas condicionales)
            'type'       => ['required', Rule::in([OfferType::DISCOUNT, OfferType::COMBO])],

            // Reglas base mínimas (se completan con sometimes)
            // amount / combo_price no se marcan como required aquí para no colisionar;
            // lo haremos con sometimes y requiredIf.
            'amount'      => ['nullable'],      // se validará si type === DISCOUNT
            'combo_price' => ['nullable'],      // se validará si type === COMBO
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required'      => 'The amount field is required',
            'combo_price.required' => 'The combo price field is required',
            'type.in'              => 'Invalid offer type.',
        ];
    }

    public function withValidator($validator)
    {
        // --- Reglas condicionales: amount si DISCOUNT
        $validator->sometimes('amount', [
            'bail',
            'required',
            'numeric',
            'max:100',
            new IniAmount(), // tu regla para porcentajes
        ], function ($input) {
            return (int)$input->type === OfferType::DISCOUNT;
        });

        // --- Reglas condicionales: combo_price si COMBO
        $validator->sometimes('combo_price', [
            'bail',
            'required',
            'numeric',
            'min:0.01', // o tu validador de precios
        ], function ($input) {
            return (int)$input->type === OfferType::COMBO;
        });

        // --- Tus chequeos de fechas (los mantengo tal cual)
        $validator->after(function ($validator) {
            if (!$this->isNotNull(request('start_date'))) {
                $validator->errors()->add('start_date', 'The start date field is required');
            }

            if (!$this->isNotNull(request('end_date'))) {
                $validator->errors()->add('end_date', 'The end date field is required');
            }

            if ($this->isNotNull(request('start_date')) && strtotime(request('end_date')) < strtotime(request('start_date'))) {
                $validator->errors()->add('end_date', 'End date can\'t be older than Start date.');
            }
            if ($this->isNotNull(request('start_date')) && $this->checkToDate()) {
                $validator->errors()->add('end_date', 'End date can\'t be older than now.');
            }
        });
    }

    private function checkToDate(): bool
    {
        $today = strtotime(date('Y-m-d H:i:s'));
        return strtotime(request('end_date')) < $today;
    }

    private function isNotNull($value): bool
    {
        return $value !== 'null';
    }
}