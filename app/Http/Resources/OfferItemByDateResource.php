<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\OfferType; // 👈 ajusta el namespace si es necesario

class OfferItemByDateResource extends JsonResource
{
    public function toArray($request): array
    {
        $items = [];

        foreach ($this as $offers) {
            if (count($offers) === 0) {
                continue;
            }

            foreach ($offers as $offer) {
                if (count($offer->offerItems) === 0) {
                    continue;
                }

                foreach ($offer->offerItems as $item) {
                    $base = (float) ($item->item?->price ?? 0);
                    $final = $base;

                    // calcula el precio final según el tipo
                    if ((int)$offer->type === OfferType::DISCOUNT) {
                        $percent = (float) ($offer->amount ?? 0); // 0..100
                        $final = $base - ($base * ($percent / 100));
                        $discountPercentFormatted = AppLibrary::convertAmountFormat($percent);
                        $comboPrice = null;
                    } else { // COMBO
                        // usa el precio de combo directamente (o amount si lo reutilizas como precio)
                        $comboPrice = (float) ($offer->combo_price ?? $offer->amount ?? $base);
                        $final = $comboPrice;
                        $discountPercentFormatted = null;
                    }

                    $items[] = [
                        "id"                                 => $item->item?->id,
                        "offer_id"                           => $offer->id,

                        // === Precio final (legacy keys conservadas) ===
                        "item_price_after_flat_discount"     => AppLibrary::flatAmountFormat($final),
                        "item_price_after_convert_discount"  => AppLibrary::convertAmountFormat($final),
                        "item_price_after_currency_discount" => AppLibrary::currencyAmountFormat($final),

                        // === Info de apoyo por tipo (nuevo, opcional) ===
                        "type"                               => (int) $offer->type,
                        "discount_percent"                   => (int)$offer->type === OfferType::DISCOUNT ? (float)($offer->amount ?? 0) : null,
                        "discount_percent_formatted"         => (int)$offer->type === OfferType::DISCOUNT ? $discountPercentFormatted : null,

                        "combo_price"                        => (int)$offer->type === OfferType::COMBO ? $comboPrice : null,
                        "combo_price_flat"                   => (int)$offer->type === OfferType::COMBO ? AppLibrary::flatAmountFormat($comboPrice) : null,
                        "combo_price_convert"                => (int)$offer->type === OfferType::COMBO ? AppLibrary::convertAmountFormat($comboPrice) : null,
                        "combo_price_currency"               => (int)$offer->type === OfferType::COMBO ? AppLibrary::currencyAmountFormat($comboPrice) : null,

                        // === Legacy: antes devolvía el "convert_discount" del amount (%).
                        // Ahora sólo tiene valor si es descuento; en combo queda null para evitar confusión.
                        "convert_discount"                   => (int)$offer->type === OfferType::DISCOUNT ? $discountPercentFormatted : null,
                    ];
                }
            }
        }

        return $items;
    }
}