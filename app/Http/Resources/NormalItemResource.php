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
            "addons"         => ItemAddonResource::collection($this->addons->load('addonItem', 'addonItem.variations', 'addonItem.offer', 'item')),

            // 👇 Solo descuenta si es DISCOUNT; si es COMBO, usa el precio del combo tal cual.
            "offer" => SimpleOfferResource::collection(
                $this->offer->filter(function ($offer) use ($price) {
                    $isActive = Carbon::now()->between($offer->start_date, $offer->end_date)
                               && $offer->status === Status::ACTIVE;
                    if (!$isActive) {
                        return false;
                    }

                    $final = $price;

                    if ((int)$offer->type === OfferType::DISCOUNT) {
                        // amount se interpreta como porcentaje (0..100)
                        $final = $price - ($price * ((float)$offer->amount / 100));
                    } elseif ((int)$offer->type === OfferType::COMBO) {
                        // combo: toma el precio directo desde BD (sin fórmulas)
                        // si no tienes columna combo_price y reutilizas amount como precio:
                        $final = $offer->combo_price ?? $offer->amount ?? $price;
                    }

                    // Inyecta los precios formateados para este offer
                    $offer->flat_price     = AppLibrary::flatAmountFormat($final);
                    $offer->convert_price  = AppLibrary::convertAmountFormat($final);
                    $offer->currency_price = AppLibrary::currencyAmountFormat($final);

                    return true;
                })
            ),
        ];
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