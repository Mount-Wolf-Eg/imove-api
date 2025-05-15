<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\JsonValidationTrait;
use Illuminate\Validation\Rule;

class SettingConsultationRequest extends FormRequest
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
            'minimum' => ['required', 'numeric', 'min:0'],
            'maximum' => ['required', 'numeric', 'gte:minimum'],

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
