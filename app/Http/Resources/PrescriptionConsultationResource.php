<?php

namespace App\Http\Resources;


use \Illuminate\Http\Request;

class PrescriptionConsultationResource extends BaseResource
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
            'consultation_id' => $this->id,
        ];
        $this->mini = [
        ];
        $this->full = [
            'prescription' => $this->prescription,
            'doctor_can_write_prescription' => $this->doctorCanWritePrescription(),
        ];
        $this->relations = [
        ];
        return $this->getResource();
    }
}
