<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\JsonValidationTrait;

class UpdateExerciseProgressRequest extends FormRequest
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
        // return auth()->check() && auth()->user()->patient !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'ease_of_exercise' => ['required', 'integer', 'min:1', 'max:10'],
            'patient_exercise_repetitions' => ['required', 'array', 'min:1'],
            'patient_exercise_repetitions.*.set_number' => ['required', 'integer', 'min:1'],
            'patient_exercise_repetitions.*.rep_number' => ['required', 'integer', 'min:0'],
            'patient_exercise_repetitions.*.hold_duration' => ['required', 'integer', 'min:0'],
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
