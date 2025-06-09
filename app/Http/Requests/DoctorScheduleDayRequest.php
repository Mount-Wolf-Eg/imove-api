<?php

namespace App\Http\Requests;

use App\Repositories\Contracts\DoctorScheduleDayContract;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\JsonValidationTrait;

class DoctorScheduleDayRequest extends FormRequest
{
    use JsonValidationTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return (bool) auth()->user()->doctor;
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        $validated['doctor_id'] = auth()->user()->doctor->id;

        if (isset($validated['schedule_repeat_from']) && isset($validated['schedule_repeat_to'])) {
            $validated['schedule_days'] = collect(CarbonPeriod::between(request('schedule_repeat_from'), request('schedule_repeat_to')))->map(function ($date) {
                $dayName = strtolower($date->format('l'));
                return [
                    'date'   => $date->format('Y-m-d'),
                    'shifts' => collect(request('schedule_days'))->firstWhere('day', $dayName)['shifts'],
                ];
            })->whereNotNull()->values()->toArray();
        }

        return $validated;
    }

    public function prepareForValidation(): void
    {
        $doctor = auth()->user()->doctor;
        if ($this->method() === 'POST' && request('date')) {
            $day = resolve(DoctorScheduleDayContract::class)->findByFilters(
                ['date' => Carbon::parse(request('date')), 'doctor' => $doctor->id]
            );
            if ($day) {
                abort(422, __('messages.date_already_exists'));
            }
        }
        if ($this->method() === 'PUT') {
            $day = $this->route('doctor_schedule_day');
            if ($day->doctor_id !== $doctor->id) {
                abort(422, __('messages.not_allowed'));
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'date'                 => config('validations.date.req') . "|after:today|before_or_equal:" . now()->addMonths(6)->toDateString(),
            'shifts'               => config('validations.array.req'),
            'shifts.*.from_time'   => config('validations.time.req'),
            'shifts.*.to_time'     => config('validations.time.req'),

            'schedule_repeat_from' => config('validations.date.null') . '|after_or_equal:' . $this->input('date', now()->toDateString()),
            'schedule_repeat_to'   => config('validations.date.null') . '|after:schedule_repeat_from',
        ];
    }

    /**
     * Customizing input names displayed for user
     * @return array
     */
    public function attributes(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [];
    }
}
