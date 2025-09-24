<?php

namespace App\Http\Resources;

use App\Enums\Status;
use App\Enums\OfferType; // 👈 ajusta el namespace si es distinto
use App\Libraries\AppLibrary;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class NormalItemResource extends JsonResource
{
    public function toArray($request)
    {
        $price = $this->price;

        return [
            "id"             => $this->id,
            "name"           => $this->name,
            "slug"           => $this->slug,
            "flat_price"     => AppLibrary::flatAmountFormat($this->price),
            "convert_price"  => AppLibrary::convertAmountFormat($this->price),
            "currency_price" => AppLibrary::currencyAmountFormat($this->price),
            "price"          => $this->price,
            "item_type"      => $this->item_type,
            "status"         => $this->status,
            "description"    => $this->description === null ? '' : $this->description,
            "caution"        => $this->caution === null ? '' : $this->caution,
            "thumb"          => $this->thumb,
            "cover"          => $this->cover,
            "preview"        => $this->preview,
            "variations"     => $this->variations->groupBy('item_attribute_id'),
            "itemAttributes" => ItemAttributeResource::collection($this->itemAttributeList($this->variations)),
            "extras"         => ItemExtraResource::collection($this->extras->load('item')),
            "addons"         => ItemAddonResource::collection($this->addons->load('addonItem', 'addonItem.variations', 'addonItem.offer', 'addonItem.comboOffer', 'item')),
            // 👇 Solo descuenta si es DISCOUNT; si es COMBO, usa el precio del combo tal cual.
            "offer" => SimpleOfferResource::collection(
                $this->mergeOffers()
                    ->unique('id')
                    ->filter(function ($offer) {
                        return Carbon::now()->between($offer->start_date, $offer->end_date)
                            && (int) $offer->status === Status::ACTIVE;
                    })
                    ->map(function ($offer) use ($price) {
                        $isDiscount = (int) $offer->type === OfferType::DISCOUNT;
                        $isComboForItem = (int) $offer->type === OfferType::COMBO
                            && (int) $offer->combo_item_id === (int) $this->id;
                        if (!$isDiscount && !$isComboForItem) {
                            return null;
                        }

                        $final = $price;
                        if ($isDiscount) {
                            $final = $price - ($price * ((float) $offer->amount / 100));
                        } elseif ($isComboForItem) {
                            $final = $offer->combo_price ?? $offer->amount ?? $price;
                        }

                        $offer->flat_price     = AppLibrary::flatAmountFormat($final);
                        $offer->convert_price  = AppLibrary::convertAmountFormat($final);
                        $offer->currency_price = AppLibrary::currencyAmountFormat($final);

                        return $offer;
                    })
                    ->filter()
                    ->values()
            ),
        ];
    }

    private function mergeOffers()
    {
        $offers = collect($this->offer ?? []);

        if ($this->relationLoaded('comboOffer') && $this->comboOffer) {
            $offers = collect([$this->comboOffer])->merge($offers);
        } elseif ($this->comboOffer) {
            $offers = collect([$this->comboOffer])->merge($offers);
        }

        return $offers;
    }

    private function itemAttributeList($variations)
    : \Vanilla\Support\Collection
    | \IlluminateAgnostic\Str\Support\Collection
    | \IlluminateAgnostic\StrAgnostic\Str\Support\Collection
    | \IlluminateAgnostic\Collection\Support\Collection
    | \IlluminateAgnostic\ArrAgnostic\Arr\Support\Collection
    | \Illuminate\Support\Collection
    | \IlluminateAgnostic\Arr\Support\Collection
    {
        $array = [];
        foreach ($variations as $b) {
            if (!isset($array[$b->itemAttribute->id])) {
                $array[$b->itemAttribute->id] = (object)[
                    'id'     => $b->itemAttribute->id,
                    'name'   => $b->itemAttribute->name,
                    'status' => $b->itemAttribute->status
                ];
            }
        }
        return collect($array);
    }
}