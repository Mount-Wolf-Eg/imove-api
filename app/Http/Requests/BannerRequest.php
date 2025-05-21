<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\JsonValidationTrait;

class BannerRequest extends FormRequest
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
        $rules =  [];
        if ($this->isMethod('post')) {
            $rules['main_image'] = 'required|'.config('validations.file.image').'|mimes:jpeg,jpg,png|max:20048';
        }else{
            $rules['main_image'] = 'nullable|'.config('validations.file.image').'|mimes:jpeg,jpg,png|max:20048';
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
            'main_image' => __('messages.image'),
        ];
    }

    /**
     * @return array
     */
    public function messages() : array
    {
        return [
            'media.image' => __('validation.main_image_mimes'),
            'media.mimes' => __('validation.main_image_mimes'),
        ];
    }
}
