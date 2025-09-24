<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleOfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) : array
    {

        $comboPrice = $this->combo_price;
        return [
            'id'                   => $this->id,
            'name'                 => $this->name,
            'type'                 => isset($this->type) ? (int) $this->type : null,
            'amount'               => $this->amount === null ? 0 : $this->amount,
            'flat_price'           => $this->flat_price,
            'convert_price'        => $this->convert_price,
            'currency_price'       => $this->currency_price,
            'combo_price'          => $comboPrice,
            'combo_price_flat'     => $comboPrice !== null ? AppLibrary::flatAmountFormat($comboPrice) : null,
            'combo_price_convert'  => $comboPrice !== null ? AppLibrary::convertAmountFormat($comboPrice) : null,
            'combo_price_currency' => $comboPrice !== null ? AppLibrary::currencyAmountFormat($comboPrice) : null,
        ];
    }
}
