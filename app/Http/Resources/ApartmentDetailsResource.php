<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'type' => $this->apartmentType?->name,
            'size' => $this->size,
            'beds_list' => $this->bedsList,
            'bathrooms' => $this->bathrooms,
            'facility_categories' => $this->facility_categories,
        ];
    }
}
