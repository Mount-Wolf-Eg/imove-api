<?php

namespace App\Http\Resources;


use \Illuminate\Http\Request;

class ExerciseResource extends BaseResource
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
            'brief' => $this->brief,
            'description' => $this->description,
            'medical_specialities' => $this->whenLoaded('medicalSpecialities', fn () => $this->medicalSpecialities->map(fn ($speciality) => [
                'id' => $speciality->id,
                'name' => $speciality->name,
            ])),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            // Add is_assigned_to_consultation based on consultation_id from request
            'is_assigned_to_consultation' => $this->getIsAssignedToConsultation($request),
        ];
        $this->full = [
        ];
        $this->relations = [
            'media' => $this->relationLoaded('media') ? new FileResource($this->media) : null,
        ];
        return $this->getResource();
    }

    
    /**
     * Determine if the exercise is assigned to the consultation provided in the request.
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

        // Check if the exercise is assigned to a program linked to the consultation
        return $this->programs()->where('consultation_id', $consultationId)->exists();
    }

}
