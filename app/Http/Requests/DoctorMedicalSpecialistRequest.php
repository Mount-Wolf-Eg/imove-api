<?php

namespace App\Http\Requests;

use App\Constants\RoleNameConstants;
use Illuminate\Foundation\Http\FormRequest;

class DoctorMedicalSpecialistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) auth()->user()->doctor;
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        $validated['user']['id'] = auth()->id();
        if (auth()->user()->doctor)
            $validated['doctor_id'] = auth()->user()->doctor->id;
        return UserRequest::prepareUserForRoles($validated, RoleNameConstants::DOCTOR->value);
    }

    public function rules(): array
    {
        return [
            'specialities' => config('validations.array.null'),
            'specialities.*' => sprintf(config('validations.model.active_null'), 'medical_specialities'),
        ];
    }
}
