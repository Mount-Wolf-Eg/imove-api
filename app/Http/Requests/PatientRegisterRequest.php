<?php

namespace App\Http\Requests;

use App\Constants\RoleNameConstants;
use App\Constants\UserGenderConstants;
use App\Traits\JsonValidationTrait;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PatientRegisterRequest extends FormRequest
{
    use JsonValidationTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function validated($key = null, $default = null): array
    {
        $validated            = parent::validated();
        $validated['phone']   = $validated['phone'] ?? auth()->user()->phone;
        $validated['user_id'] = auth()->id();
        return UserRequest::prepareUserForRoles($validated, RoleNameConstants::PATIENT->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:250|regex:/^(\b[\pL\pM]+\b\s+){2}\b[\pL\pM]+\b$/u',
            'gender' => config('validations.integer.req') . '|in:' . implode(',', UserGenderConstants::values()),
            'national_id' => config('validations.integer.req') , // . '|unique:patients,national_id,Null,' . auth()->id(),
            // 'date_of_birth' => config('validations.date.req'), // old
            'date_of_birth' => sprintf(config('validations.date.null_after'), '1960-12-31'),
            // 'phone' => config('validations.phone.req') . '|unique:users,phone', // old
            'city_id' => sprintf(config('validations.model.null'), 'cities'), // new null
            'image' => sprintf(config('validations.model.null'), 'files')
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('attributes.name'),
            'gender' => __('attributes.gender'),
            'national_id' => __('attributes.national_id'),
            'date_of_birth' => __('attributes.date_of_birth'),
            'phone' => __('attributes.phone'),
            'city_id' => __('attributes.city_id'),
            'image' => __('attributes.image')
        ];
    }

    public function messages()
    {
        return [
            'name.required' => trans('The name field is required'),
            'name.regex' => trans('The name must consist of three words: first name, middle name, and last name'),
        ];
    }
}
