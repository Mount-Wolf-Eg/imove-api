<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\JsonValidationTrait;

class RegionRequest extends FormRequest
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
            'name.ar' => config('validations.string.req') . '|unique:regions,name->ar,' .$this->route('regions')?->id,
            'name.en' => config('validations.string.req') . '|unique:regions,name->en,' .$this->route('regions')?->id
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
            'name.en' => __('messages.name_en')
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
