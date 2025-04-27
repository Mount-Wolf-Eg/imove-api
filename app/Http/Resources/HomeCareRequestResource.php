<?php

namespace App\Http\Resources;

use \Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeCareRequestResource extends BaseResource
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
            'status' => $this->status,
            'status_label' => $this->statusLabel(),
            'patient' => $this->patient_id,
            'city_id' => $this->city_id,
            'city_name' => $this->city?->name,
            'medical_speciality_id'   => $this->medical_speciality_id,
            'medical_speciality_name' => $this->medicalSpeciality?->name,
            'address' => $this->address,
            'description' => $this->description,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
        $this->full = [
        ];
        //$this->relationLoaded()
        $this->relations = [
        ];
        return $this->getResource();
    }


    private function statusLabel(): string
    {
        return match ($this->status) {
            1 => 'Pending',
            2 => 'Visited',
            3 => 'Rejected',
            default => 'Unknown',
        };
    }

}
