<?php

namespace App\Http\Resources;

use App\Enums\Status;
use App\Enums\OfferType; // 👈 ajusta el namespace si es distinto
use App\Libraries\AppLibrary;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemAddonResource extends JsonResource
{
    public $variation = 0;

    public function toArray($request)
    {
        $this->variation = $this->variationTotal();

        // Precio base del addon
        $basePrice = $this?->addonItem?->price ?? 0.0;

        $addonItemId = optional($this->addonItem)->id;
        $offers = $this->collectAddonOffers()
            ->filter(function ($offer) {
                return Carbon::now()->between($offer->start_date, $offer->end_date)
                    && (int) $offer->status === Status::ACTIVE;
            })
            ->map(function ($offer) use ($basePrice, $addonItemId) {
                $isDiscount = (int) $offer->type === OfferType::DISCOUNT;
                $isComboForAddon = (int) $offer->type === OfferType::COMBO
                    && (int) $offer->combo_item_id === (int) $addonItemId;
                if (!$isDiscount && !$isComboForAddon) {
                    return null;
                }

                // Inyecta formateos SIN volver a descontar
                if ($isDiscount) {
                    $final = $basePrice - ($basePrice * ((float) $offer->amount / 100));
                } else {
                    $final = $offer->combo_price ?? $offer->amount ?? $basePrice;
                }

                $offer->flat_price     = AppLibrary::flatAmountFormat($final);
                $offer->convert_price  = AppLibrary::convertAmountFormat($final);
                $offer->currency_price = AppLibrary::currencyAmountFormat($final);
                $offer->final_numeric  = $final;

                return $offer;
            })->filter();

        // Toma la primera oferta activa (si existe) para el total
        $offerPriceNumeric = $offers->isNotEmpty()
            ? (float) ($offers->first()->final_numeric ?? $basePrice)
            : (float) $basePrice;

        $variationNumeric = (float) ($this->variation?->price ?? 0);
        $totalNumeric     = $variationNumeric + $offerPriceNumeric;

        return [
            'id'                             => $this->id,
            'item_id'                        => $this->item_id,
            'item_addon_id'                  => $this->addon_item_id,

            'item_name'                      => optional($this->item)->name,
            'addon_item_name'                => optional($this->addonItem)->name,

            'addon_item_price'               => optional($this->addonItem)->price,
            'addon_item_flat_price'          => AppLibrary::flatAmountFormat(optional($this->addonItem)->price),
            'addon_item_convert_price'       => AppLibrary::convertAmountFormat(optional($this->addonItem)->price),
            'addon_item_currency_price'      => AppLibrary::currencyAmountFormat(optional($this->addonItem)->price),

            'addon_item_status'              => optional($this->addonItem)->status,

            'variations'                     => json_decode($this->addon_item_variation),
            'variation_total'                => $variationNumeric,
            'variation_total_flat_price'     => AppLibrary::flatAmountFormat($variationNumeric),
            'variation_total_convert_price'  => AppLibrary::convertAmountFormat($variationNumeric),
            'variation_total_currency_price' => AppLibrary::currencyAmountFormat($variationNumeric),

            'total'                          => $totalNumeric,
            'total_flat_price'               => AppLibrary::flatAmountFormat($totalNumeric),
            'total_convert_price'            => AppLibrary::convertAmountFormat($totalNumeric),
            'total_currency_price'           => AppLibrary::currencyAmountFormat($totalNumeric),

            'variation_names'                => $this->variation?->name,

            'thumb'                          => $this->addonItem?->thumb,
            'cover'                          => $this->addonItem?->cover,
            'preview'                        => $this->addonItem?->preview,
            'caution'                        => optional($this->addonItem?->caution) == null ? '' : optional($this->addonItem)->caution,

            // 👇 Ofertas ya con precio final en cada una (sin doble descuento)
            'offer'                          => SimpleOfferResource::collection($offers),
        ];
    }

    private function collectAddonOffers()
    {
        if (!$this->addonItem) {
            return collect();
        }

        $offers = collect($this->addonItem->offer ?? []);

        if ($this->addonItem->relationLoaded('comboOffer') && $this->addonItem->comboOffer) {
            $offers = collect([$this->addonItem->comboOffer])->merge($offers);
        } elseif ($this->addonItem->comboOffer) {
            $offers = collect([$this->addonItem->comboOffer])->merge($offers);
        }

        return $offers;
    }

    private function variationTotal()
    {
        $variationArray = $this->addonItem?->variations?->mapWithKeys(function ($variation) {
            return [$variation->id => $variation];
        });

        if ($this->addon_item_variation) {
            $variations = (object) json_decode($this->addon_item_variation, true);
            $price      = 0;
            $name       = [];

            foreach ($variations as $variation) {
                if (isset($variationArray[$variation])) {
                    $name[] = [
                        'id'             => $variationArray[$variation]->id,
                        'name'           => $variationArray[$variation]->name,
                        'attribute_name' => $variationArray[$variation]->itemAttribute->name
                    ];
                    $price  += $variationArray[$variation]->price;
                }
            }

            return (object)[
                'price' => $price,
                'name'  => $name
            ];
        }

        return null;
    }
}