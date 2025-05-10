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
        ];
        $this->full = [
        ];
        $this->relations = [
            'media' => $this->relationLoaded('media') ? new FileResource($this->media) : null,
        ];
        return $this->getResource();
    }
}
