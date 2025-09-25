<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryComplementResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description ?? '',
            'created_at'  => optional($this->created_at)->toDateTimeString(),
            'updated_at'  => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}