<?php

namespace App\Http\Resources;


use \Illuminate\Http\Request;

class ConsultationInDoctorResource extends BaseResource
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
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
            ],
            'type' => [
                'value' => $this->type->value,
                'label' => $this->type->label(),
            ],
        ];
        $this->mini = [
            'patient_start_at' => $this->patient_start_at?->format('H:i')?? null,
            'doctor_start_at' => $this->doctor_start_at?->format('H:i')?? null,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
        $this->full = [
            'patient_description' => $this->patient_description,
            'doctor_description' => $this->doctor_description,
            'medical_review' => $this->medical_review,
            'total_amount' => $this->total_amount,
            'contact_type' => [
                'value' => $this->contact_type?->value,
                'label' => $this->contact_type?->label(),
            ],
        ];
        $this->relations = [
            'doctorScheduleDayShift' => $this->relationLoaded('doctorScheduleDayShift') ? new DoctorScheduleDayShiftResource($this->doctorScheduleDayShift) : null,
        ];
        return $this->getResource();
    }
}
