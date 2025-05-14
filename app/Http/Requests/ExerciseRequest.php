<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\JsonValidationTrait;

class ExerciseRequest extends FormRequest
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
            'specialities' => config('validations.array.req'),
            'specialities.*' => sprintf(config('validations.model.active_req'), 'medical_specialities'),
            // 'medical_speciality_id' => sprintf(config('validations.model.active_req'), 'medical_specialities'),
            'name.ar' => config('validations.string.req'),
            'name.en' => config('validations.string.req'),
            'brief.ar' => config('validations.string.req'),
            'brief.en' => config('validations.string.req'),
            'description.ar' => config('validations.long_text.req'),
            'description.en' => config('validations.long_text.req'),
        ];
        if ($this->isMethod('post')) {
            $rules['media'] = 'required|'.config('validations.file.mixed').'|max:20048';
            $rules['main_image'] = 'required|'.config('validations.file.image').'|mimes:jpeg,jpg,png|max:2048';
        }else{
            $rules['media'] = 'nullable|'.config('validations.file.mixed').'|max:20048';
            $rules['main_image'] = 'nullable|'.config('validations.file.image').'|mimes:jpeg,jpg,png|max:2048';
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
            'brief.ar' => __('messages.brief_ar'),
            'brief.en' => __('messages.brief_en'),
            'description.ar' => __('messages.description_ar'),
            'description.en' => __('messages.description_en'),
            'media' => __('messages.video'),
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
