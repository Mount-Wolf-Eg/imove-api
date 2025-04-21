<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\JsonValidationTrait;

class TechnicalSupportRequest extends FormRequest
{
    use JsonValidationTrait;

    public function authorize(): bool
    {
        return true;
    }

 
    public function rules(): array
    {
        return [
            'topic' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ];
    }

    // public function validated($key = null, $default = null)
    // {
    //     $data = parent::validated($key, $default);
    //     // Add any additional processing of the validated data here
    //     $data['user_id'] = auth()->id();
    //     return $data;
    // }

    public function attributes() : array
    {
        return [];
    }


    public function messages() : array
    {
        return [];
    }
}
