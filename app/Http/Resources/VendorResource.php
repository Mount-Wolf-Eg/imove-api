<?php

namespace App\Http\Resources;


use \Illuminate\Http\Request;

class VendorResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request) : array
    {
        $this->micro = [
            'id' => $this->id,
        ];
        $this->mini = [
            'is_active' => $this->is_active,
            'active_status' => $this->active_status,
            'active_class' => $this->active_class,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
        $this->full = [
        ];
        $this->relations = [
            'user' => $this->relationLoaded('user') ? new UserResource($this->user) : null,
            'city' => $this->relationLoaded('city') ? new CityResource($this->city) : null,
            'icon' => $this->relationLoaded('icon') ? new FileResource($this->icon) : null,
        ];
        return $this->getResource();
    }
}
