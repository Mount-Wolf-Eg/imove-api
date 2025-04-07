<?php

namespace App\Http\Requests;

use App\Constants\RoleNameConstants;
use App\Repositories\Contracts\RoleContract;
use App\Traits\JsonValidationTrait;
use Carbon\CarbonPeriod;
use Illuminate\Foundation\Http\FormRequest;

class DoctorRegisterRequest extends FormRequest
{
    use JsonValidationTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    public function validated($key = null, $default = null)
    {
        $validated              = parent::validated();
        $validated['user_id']   = auth()->id();
        $validated['role']      = resolve(RoleContract::class)->findBy('name', RoleNameConstants::DOCTOR->value);
        $validated['is_active'] = false;

        return $validated;

        // return array_merge($validated, DoctorScheduleRequest::afterValidation($validated));
    }

    public function rules(): array
    {
        $rules = [
            'specialities'                          => config('validations.array.req'),
            'specialities.*'                        => sprintf(config('validations.model.active_req'), 'medical_specialities'),
            'academic_degree_id'                    => sprintf(config('validations.model.active_req'), 'academic_degrees'),
            'national_id'                           => config('validations.string.req') . '|regex:/^[1-4]/',
            'medical_id'                            => config('validations.string.req'),
            'city_id'                               => sprintf(config('validations.model.active_null'), 'cities'),
            'experience_years'                      => config('validations.integer.null'),
            'bio'                                   => config('validations.string.null'),
            'attachments'                           => config('validations.array.null'),
            'attachments.*'                         => sprintf(config('validations.model.req'), 'files'),
            'urgent_consultation_enabled'           => config('validations.boolean.null'),
            'with_appointment_consultation_enabled' => config('validations.boolean.null'),
        ];

        return $rules;
        // return array_merge($rules, (new DoctorScheduleRequest())->rules());
    }
}
