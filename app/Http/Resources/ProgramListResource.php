<?php

namespace App\Http\Resources;

use \Illuminate\Http\Request;

class ProgramListResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $locale = $request->header('Accept-Language', config('app.locale'));

        $this->micro = [
            'id' => $this->id,
        ];
        $this->mini = [
            'consultation_id' => $this->consultation_id,
            'consultation_created_at' => $this->consultation?->created_at?->format('Y-m-d H:i:s'),
            'doctor_name' => $this->consultation?->doctor->user->name,
            'diagnosis'   => $this->diagnosis,
            'num_of_sessions_per_day' => $this->num_of_sessions_per_day,
            'num_of_days_of_week'     => $this->num_of_days_of_week,
            'num_of_weeks' => $this->num_of_weeks,
            'break_between_exercises' => $this->break_between_exercises,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
        $this->full = [];
        $this->relations = [
            'doctor_medical_specialities' => $this->whenLoaded('consultation.doctor.medicalSpecialities') ? MedicalSpecialityResource::collection($this->consultation->doctor->medicalSpecialities) : [],
            'doctor_avatar' => $this->whenLoaded('consultation.doctor.user.avatar') ? new FileResource($this->consultation->doctor->user->avatar) : null,
            // 'consultation' => $this->whenLoaded('consultation', fn () => [
            //     'id' => $this->consultation->id,
            //     'patient_id' => $this->consultation->patient_id,
            // ]),
            // 'sessions' => $this->whenLoaded('sessions', fn () => $this->sessions->map(fn ($session) => [
            //     'id' => $session->id,
            //     'session_date' => $session->session_date,
            // ])),
        ];

        return $this->getResource();
    }
}
