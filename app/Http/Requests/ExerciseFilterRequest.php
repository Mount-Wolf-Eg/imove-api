<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\JsonValidationTrait;

class ExerciseFilterRequest extends FormRequest
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
            'medical_speciality_ids'   => 'nullable|array',
            'medical_speciality_ids.*' => 'exists:medical_specialities,id',
            'keyword' => 'nullable|string',
            'page'  => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1',
            'order' => 'nullable|array',
            'order.*' => 'in:asc,desc',
        ];
    }


    /**
     * Customizing input names displayed for user
     * @return array
     */
    public function attributes() : array
    {
        return [];
    }

    /**
     * @return array
     */
    public function messages() : array
    {
        return [];
    }

}
