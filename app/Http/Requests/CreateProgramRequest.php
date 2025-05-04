<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\JsonValidationTrait;

class CreateProgramRequest extends FormRequest
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
            'consultation_id' => ['required', 'exists:consultations,id'],
            'diagnosis' => ['required', 'string', 'max:255'],
            'num_of_sessions_per_day' => ['required', 'integer', 'min:1'],
            'num_of_days_of_week' => ['required', 'integer', 'min:1', 'max:7'],
            'num_of_weeks' => ['required', 'integer', 'min:1'],
            'break_between_exercises' => ['required', 'integer', 'min:0'],
            'exercises' => ['required', 'array', 'min:1'],
            'exercises.*.exercise_id' => ['required', 'exists:exercises,id'],
            'exercises.*.sets' => ['required', 'integer', 'min:1'],
            'exercises.*.break_between_sets' => ['nullable', 'integer', 'min:0'],
            'exercises.*.weight' => ['nullable', 'integer', 'min:0'],
            'exercises.*.rep' => ['nullable', 'integer', 'min:1'],
            'exercises.*.hold_duration' => ['required', 'integer', 'min:0'],
            'exercises.*.comments' => ['nullable', 'string', 'max:255'],
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
