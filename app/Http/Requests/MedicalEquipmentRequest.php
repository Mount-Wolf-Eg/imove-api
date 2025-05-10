<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\JsonValidationTrait;

class MedicalEquipmentRequest extends FormRequest
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
        $rules =  [
            'name.ar' => config('validations.string.req'),
            'name.en' => config('validations.string.req'),
            'link'    => config('validations.text.req'),
            'category_id' => sprintf(config('validations.model.active_req'), 'category_medical_equipment'),
        ];
        if($this->method() === 'POST'){
            $rules['photo'] = 'required|'.config('validations.file.image').'|mimes:jpeg,jpg,png|max:2048';
        }else{
            $rules['photo'] = 'nullable|'.config('validations.file.image').'|max:2048';
        }
        return $rules;

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
            'link'    => __('messages.link'),
            'category_id' => __('messages.type'),
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
