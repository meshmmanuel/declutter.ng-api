<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class WebsiteFormTransformer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            "id" => $this->id,
            "name" => $this->name,
            "state" => $this->state,
            "email" => $this->email,
            "phone" => $this->phone,
            "items_interested" => $this->items_interested,
            "items_to_sell" => $this->items_to_sell,
            "updated_at" => $this->updated_at,
            "created_at" => $this->created_at
        ];
    }
}
