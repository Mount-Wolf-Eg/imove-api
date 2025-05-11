<?php

namespace App\Http\Resources;


use \Illuminate\Http\Request;

class EducationalContentResource extends BaseResource
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
            'title' => $this->title,
        ];
        $this->mini = [
            'is_active' => $this->is_active,
            'active_status' => $this->active_status,
            'active_class' => $this->active_class,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
        $this->full = [
            'title_locales' => $this->getTranslations('title'),
            'content_locales' => $this->getTranslations('content'),
            'content' => $this->content,
            'views' => $this->views,
            'medical_speciality_id' => $this->medical_speciality_id,
            'medical_speciality_name' => $this->medical_speciality?->name,
            'likes' => $this->likes,
            'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
            // Add is_assigned_to_consultation based on consultation_id from request
            'is_assigned_to_consultation' => $this->getIsAssignedToConsultation($request),
        ];
        $this->relations = [
            // 'main_image' => $this->relationLoaded('mainImage') ? null : null,
            'main_image' => $this->relationLoaded('mainImage') ? new FileResource($this->mainImage) : null,
            'author' => $this->relationLoaded('author') ? new UserResource($this->author) : null,
            'likes_count' => $this->relationLoaded('likes') ? $this->likes->count() : 0,
            'auth_like_status' => $this->relationLoaded('likes') ? $this->auth_like_status : false,
            // 'medical_speciality' => $this->relationLoaded('medicalSpeciality') ? new MedicalSpecialityResource($this->medicalSpeciality) : null,
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
