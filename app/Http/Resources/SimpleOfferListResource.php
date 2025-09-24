<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\OfferType; // 👈 ajusta el namespace

class SimpleOfferListResource extends JsonResource
{
    public function toArray($request): array
    {
        $type = (int) $this->type;
        $typeLabel = match ($type) {
            OfferType::DISCOUNT => 'Descuento',
            OfferType::COMBO    => 'Combo',
            default             => 'Desconocido',
        };

        // Valores “correctos” según type
        $discountPercent = null;
        $discountPercentFlat = null;
        $discountPercentFormatted = null;

        $comboPrice = null;
        $comboPriceFlat = null;
        $comboPriceFormatted = null;

        if ($type === OfferType::DISCOUNT) {
            $discountPercent           = $this->amount ?? 0; // porcentaje 0–100
            $discountPercentFlat       = AppLibrary::flatAmountFormat($discountPercent);
            $discountPercentFormatted  = AppLibrary::convertAmountFormat($discountPercent);
        } elseif ($type === OfferType::COMBO) {
            // si no tienes columna combo_price y reutilizas amount, usa $this->amount
            $comboPrice                = $this->combo_price ?? 0;
            $comboPriceFlat            = AppLibrary::flatAmountFormat($comboPrice);
            $comboPriceFormatted       = AppLibrary::convertAmountFormat($comboPrice);
        }

        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'slug'               => $this->slug,

            // Tipo explícito
            'type'               => $type,
            'type_label'         => $typeLabel,
            'combo_item_id'      => $this->combo_item_id,

            // Compatibilidad (legacy)
            'amount'             => $this->amount === null ? 0 : $this->amount,
            'flat_amount'        => AppLibrary::flatAmountFormat($this->amount),
            'convert_amount'     => AppLibrary::convertAmountFormat($this->amount),

            // Campos “correctos” según tipo
            'discount_percent'           => $discountPercent,
            'discount_percent_flat'      => $discountPercentFlat,
            'discount_percent_formatted' => $discountPercentFormatted,

            'combo_price'                => $comboPrice,
            'combo_price_flat'           => $comboPriceFlat,
            'combo_price_formatted'      => $comboPriceFormatted,

            // Fechas / estado / imagen
            'convert_start_date' => AppLibrary::datetime($this->start_date),
            'convert_end_date'   => AppLibrary::datetime($this->end_date),
            'start_date'         => $this->start_date,
            'end_date'           => $this->end_date,
            'status'             => $this->status,
            'image'              => $this->cover,
        ];
    }
}