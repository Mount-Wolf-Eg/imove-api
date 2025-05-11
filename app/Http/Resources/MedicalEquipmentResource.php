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
            // Add is_assigned_to_consultation based on consultation_id from request
            'is_assigned_to_consultation' => $this->getIsAssignedToConsultation($request),
        ];
        $this->full = [
        ];
        //$this->relationLoaded()
        $this->relations = [
            'photo' => $this->relationLoaded('photo') ? new FileResource($this->photo) : null,
        ];
 
        return $this->getResource();
    }

    /**
     * Determine if the medical equipment is assigned to the consultation provided in the request.
     *
     * @param Request $request
     * @return bool|null
     */
    protected function getIsAssignedToConsultation(Request $request): ?bool
    {
        $consultationId = $request->query('consultation_id');

        if (!$consultationId) {
            return null; // Return null if no consultation_id is provided
        }

        // Check if the medical equipment is assigned to the consultation
        return $this->consultations()->where('consultation_id', $consultationId)->exists();
    }

}
