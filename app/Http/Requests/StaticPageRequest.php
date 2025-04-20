<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\JsonValidationTrait;

class StaticPageRequest extends FormRequest
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
            'content.ar' => config('validations.text.req'),
            'content.en' => config('validations.text.req')
        ];
    }

    /**
     * Customizing input names displayed for user
     * @return array
     */
    public function attributes() : array
    {
        return [
            'content.ar' => __('messages.content_ar'),
            'content.en' => __('messages.content_en')
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
