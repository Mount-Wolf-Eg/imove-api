<?php

namespace App\Http\Resources;


use \Illuminate\Http\Request;

class MedicalEquipmentResource extends BaseResource
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
            'name' => $this->name,
            'category_id' => $this->category_id,
            'category_name' => $this->category->name?? null,
            'link' => $this->link,
            // 'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
        $this->full = [
        ];
        //$this->relationLoaded()
        $this->relations = [
            'photo' => $this->relationLoaded('photo') ? new FileResource($this->photo) : null,
        ];
 
        return $this->getResource();
    }
}
