<?php

namespace App\Http\Resources;


use \Illuminate\Http\Request;

class BannerResource extends BaseResource
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
        ];
        $this->full = [
        ];
        $this->relations = [
            // 'main_image' => $this->relationLoaded('mainImage') ? null : null,
            'main_image' => $this->relationLoaded('mainImage') ? new FileResource($this->mainImage) : null,
            
        ];
        return $this->getResource();
    }

    


}
