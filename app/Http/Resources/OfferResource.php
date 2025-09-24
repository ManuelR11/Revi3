<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\OfferType;

class OfferResource extends JsonResource
{
    public function toArray($request): array
    {
        // 1) Mapea el label del tipo
        $type = (int) $this->type; // int o string según tu implementación
        $typeLabel = match ($type) {
            OfferType::DISCOUNT => 'Descuento',
            OfferType::COMBO    => 'Combo',
            default             => 'Desconocido',
        };

        // 2) Calcula el “campo monetario correcto” según el tipo
        //    - Para descuento: amount es porcentaje (0–100)
        //    - Para combo:    combo_price es dinero
        $discountPercent = null;
        $discountPercentFlat = null;
        $discountPercentFormatted = null;

        $comboPrice = null;
        $comboPriceFlat = null;
        $comboPriceFormatted = null;

        if ($type === OfferType::DISCOUNT) {
            // amount = porcentaje
            $discountPercent = $this->amount ?? 0;
            // Si tus helpers están pensados para dinero, puedes seguir exponiendo “flat/convert”
            // para consistencia (o crear helpers específicos de % si los tienes).
            $discountPercentFlat = AppLibrary::flatAmountFormat($discountPercent);
            $discountPercentFormatted = AppLibrary::convertAmountFormat($discountPercent);
        } elseif ($type === OfferType::COMBO) {
            // combo_price = dinero (asegúrate de que exista en el modelo)
            $comboPrice = $this->combo_price ?? 0;
            $comboPriceFlat = AppLibrary::flatAmountFormat($comboPrice);
            $comboPriceFormatted = AppLibrary::convertAmountFormat($comboPrice);
        }

        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'slug'               => $this->slug,

            // === Tipo ===
            'type'               => $type,
            'type_label'         => $typeLabel,
            'combo_item_id'      => $this->combo_item_id,

            // === Compatibilidad (legacy) ===
            // amount original; útil si aún tienes clientes antiguos
            'amount'             => $this->amount === null ? 0 : $this->amount,
            'flat_amount'        => AppLibrary::flatAmountFormat($this->amount),
            'convert_amount'     => AppLibrary::convertAmountFormat($this->amount),

            // === Nuevos campos explícitos ===
            // Para DESCUENTO
            'discount_percent'           => $discountPercent,              // número (0–100)
            'discount_percent_flat'      => $discountPercentFlat,          // si usas estos helpers para mostrar
            'discount_percent_formatted' => $discountPercentFormatted,

            // Para COMBO
            'combo_price'                => $comboPrice,                   // número (dinero)
            'combo_price_flat'           => $comboPriceFlat,
            'combo_price_formatted'      => $comboPriceFormatted,

            // === Otras propiedades ===
            'status'             => $this->status,
            'image'              => $this->cover,
            'convert_start_date' => AppLibrary::datetime($this->start_date),
            'convert_end_date'   => AppLibrary::datetime($this->end_date),
            'start_date'         => $this->start_date,
            'end_date'           => $this->end_date,

            'items'              => SimpleItemResource::collection(
                $this->items->load('offer', 'category')
            ),
        ];
    }
}