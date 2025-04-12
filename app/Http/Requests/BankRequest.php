<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\JsonValidationTrait;

class BankRequest extends FormRequest
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
        // check the user has one bank account
        if ($this->isMethod('post')) {
            $user = auth()->user();
            if ($user->banks()->count() > 0) abort(422, 'You can only have one bank account');
        }
        
        // check the user has one bank account
        return [
            'name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'iban' => 'required|string|max:255',
            // Add other validation rules as needed
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
