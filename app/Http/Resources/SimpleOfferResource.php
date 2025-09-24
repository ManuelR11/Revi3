<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\OfferType; // 👈 Ajusta el namespace a tu enum/constantes

class SimpleOfferResource extends JsonResource
{
    public function toArray($request): array
    {
        $type = (int) $this->type;
        $typeLabel = match ($type) {
            OfferType::DISCOUNT => 'Descuento',
            OfferType::COMBO    => 'Combo',
            default             => 'Desconocido',
        };

        // Inicializa valores
        $discountPercent = null;
        $comboPrice = null;
        $flat = null;
        $convert = null;
        $currency = null;

        if ($type === OfferType::DISCOUNT) {
            $discountPercent = $this->amount ?? 0;
            $flat     = AppLibrary::flatAmountFormat($discountPercent);
            $convert  = AppLibrary::convertAmountFormat($discountPercent);
            $currency = AppLibrary::currencyFormat($discountPercent ?? 0); // si tienes helper para %
        } elseif ($type === OfferType::COMBO) {
            // ⚠️ Si no tienes columna combo_price y reusas amount, cámbialo por $this->amount
            $comboPrice = $this->combo_price ?? $this->amount ?? 0;
            $flat     = AppLibrary::flatAmountFormat($comboPrice);
            $convert  = AppLibrary::convertAmountFormat($comboPrice);
            $currency = AppLibrary::currencyFormat($comboPrice);
        }

        return [
            'id'   => $this->id,
            'name' => $this->name,

            // === Tipo explícito
            'type'       => $type,
            'type_label' => $typeLabel,

            // === Compatibilidad (legacy)
            'amount'         => $this->amount === null ? 0 : $this->amount,
            'flat_price'     => $this->flat_price,     // si tu modelo los expone directamente
            'convert_price'  => $this->convert_price,
            'currency_price' => $this->currency_price,

            // === Nuevos campos explícitos
            'discount_percent' => $discountPercent,
            'combo_price'      => $comboPrice,
            'flat_value'       => $flat,
            'convert_value'    => $convert,
            'currency_value'   => $currency,
        ];
    }
}