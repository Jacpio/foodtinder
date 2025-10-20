<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DishResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'image_url'   => $this->image_url_full,
            'category'    => $this->whenLoaded('category', fn() => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ]),
            'cuisine'     => $this->whenLoaded('cuisine', fn() => [
                'id' => $this->cuisine->id,
                'name' => $this->cuisine->name,
            ]),
            'flavour'     => $this->whenLoaded('flavour', fn() => [
                'id' => $this->flavour->id,
                'name' => $this->flavour->name,
            ]),
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}
