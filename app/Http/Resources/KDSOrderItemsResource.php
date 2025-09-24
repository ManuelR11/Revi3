<?php

namespace App\Http\Resources;

use App\Models\OfferItem;
use Illuminate\Http\Resources\Json\JsonResource;

class KDSOrderItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'item_id'         => $this->item_id,
            'item_name'       => $this->orderItem?->name,
            'quantity'        => $this->quantity,
            'item_variations' => json_decode($this->item_variations),
            'item_extras'     => json_decode($this->item_extras),
            'instruction'     => $this->instruction,
            'combo_items'     => $this->comboItems(),
        ];
    }
    private function comboItems(): array
    {
        $item = $this->orderItem;

        if (!$item) {
            return [];
        }

        $item->loadMissing('comboOffer.offerItems.item');

        $comboOffer = $item->comboOffer;

        if (!$comboOffer) {
            return [];
        }

        return $comboOffer->offerItems
            ->map(function (OfferItem $offerItem) {
                if (!$offerItem->item) {
                    return null;
                }

                return [
                    'item_id'   => $offerItem->item_id,
                    'item_name' => $offerItem->item->name,
                    'quantity'  => $offerItem->quantity ?? 1,
                ];
            })
            ->filter()
            ->values()
            ->toArray();
    }
}
