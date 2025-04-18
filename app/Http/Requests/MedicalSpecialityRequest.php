<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\JsonValidationTrait;

class MedicalSpecialityRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name.ar' => config('validations.string.req') . '|unique:medical_specialities,name->ar,' .$this->route('medical_speciality')?->id,
            'name.en' => config('validations.string.req') . '|unique:medical_specialities,name->en,' .$this->route('medical_speciality')?->id,
            'description.ar' => config('validations.string.null'),
            'description.en' => config('validations.string.null'),
            'percentage' => config('validations.double.req') . '|min:1|max:100',

            'position' => 'required|min:front,back',

            'icon' => sprintf(config('validations.model.null'), 'files')
        ];
    }

    /**
     * Customizing input names displayed for user
     * @return array
     */
    public function attributes() : array
    {
        return [
            'name.ar' => __('messages.name_ar'),
            'name.en' => __('messages.name_en'),
            'description.ar' => __('messages.description_ar'),
            'description.en' => __('messages.description_en'),
            'percentage' => __('messages.percentage'),

            'icon' => __('attributes.image')
        ];
    }

    /**
     * @return array
     */
    public function messages() : array
    {
        return [];
    }
}
