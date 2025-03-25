<?php

namespace App\Http\Requests;

use App\Constants\ConsultationContactTypeConstants;
use App\Constants\ConsultationPaymentTypeConstants;
use App\Constants\ConsultationStatusConstants;
use App\Constants\ConsultationTypeConstants;
use App\Constants\ReminderConstants;
use App\Models\Doctor;
use App\Repositories\Contracts\ConsultationContract;
use App\Repositories\Contracts\DoctorContract;
use App\Repositories\Contracts\DoctorScheduleDayShiftContract;
use App\Rules\ValidCouponRule;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\JsonValidationTrait;

class ConsultationRequest extends FormRequest
{
    use JsonValidationTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return (bool)auth()->user()->patient;
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        $validated['patient_id'] = $validated['patient_id'] ?? $this->user()->patient?->id;

        if (! isset($validated['doctor_id'])) {
            return $validated;
        }

        if (isset($validated['doctor_id']) && isset($validated['type']) && $validated['type'] == ConsultationTypeConstants::WITH_APPOINTMENT->value) {
            $shiftTaken = resolve(ConsultationContract::class)->findBy('doctor_schedule_day_shift_id', $validated['doctor_schedule_day_shift_id'], false);

            if ($shiftTaken) {
                throw new ValidationException(__('messages.schedule_slot_expired'));
            }

            $validated['amount'] = resolve(DoctorContract::class)->find($validated['doctor_id'])->with_appointment_consultation_price;
            $scheduleSlot = resolve(DoctorScheduleDayShiftContract::class)->find($validated['doctor_schedule_day_shift_id']);
            $actualTime = Carbon::parse($scheduleSlot->day->date->format('Y-m-d') . ' ' . $scheduleSlot->from_time->format('H:i:s'));

            if ($actualTime->isPast()) {
                throw new ValidationException(__('messages.schedule_slot_expired'));
            } elseif (isset($validated['reminder_before'])) {
                $scheduleDay = $scheduleSlot->day;
                $scheduleTime = $scheduleDay->date->format('Y-m-d') . ' ' . $scheduleSlot->from_time->format('H:i:s');
                $scheduleTime = Carbon::parse($scheduleTime);
                $validated['reminder_at'] = $scheduleTime->subMinutes($validated['reminder_before']);
                unset($validated['reminder_before']);
            }

            $validated['is_active'] = false;
        }

        if (! isset($validated['contact_type']) || $validated['contact_type'] == null) {
            $validated['contact_type'] = ConsultationContactTypeConstants::AUDIO->value;
        }

        if (! isset($validated['medical_speciality_id']) || $validated['medical_speciality_id'] == null && isset($validated['doctor_id'])) {
            $validated['medical_speciality_id'] = Doctor::find($validated['doctor_id'])->medicalSpecialities->first()->id;
        }

        return $validated;
    }

    public function prepareForValidation(): void
    {
        if ((int) request('type') === ConsultationTypeConstants::URGENT->value) {
            $filters = [
                'type' => ConsultationTypeConstants::URGENT->value,
                'medicalSpeciality' => request('medical_speciality_id'),
                'status' => [
                    ConsultationStatusConstants::PENDING,
                    ConsultationStatusConstants::URGENT_HAS_DOCTORS_REPLIES
                ],
            ];
            $patient = auth()->user()->patient;
            $filters['patient'] = $patient->id;
            $patientCount = resolve(ConsultationContract::class)->countWithFilters($filters);
            $relative = request('patient_id');
            $relativesCount = 0;
            if ($relative) {
                $filters['patient'] = $patient->relatives->where('id', request('patient_id'))->first()?->id;
                $relativesCount = resolve(ConsultationContract::class)->countWithFilters($filters);
            }
            if (($patientCount && !request('patient_id')) || $relativesCount) {
                abort(422, __('messages.new_urgent_consultation_validation'));
            }
        }

        if ((int) request(('payment_type')) === ConsultationPaymentTypeConstants::WALLET->value && request('doctor_id')) {
            $amount = Doctor::find(request('doctor_id'))->with_appointment_consultation_price;
            if ($amount > auth()->user()->wallet) {
                abort(422, __('messages.insufficient_wallet_balance'));
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
        if (! $this->consultation) {
            return [
                'patient_id' => sprintf(config('validations.model.active_null'), 'patients'),
                'doctor_id' => sprintf(config('validations.model.active_null'), 'doctors') . '|required_if:type,==,' . ConsultationTypeConstants::WITH_APPOINTMENT->value,

                'type' => config('validations.integer.req') . '|in:' . implode(',', ConsultationTypeConstants::values()),
                'doctor_schedule_day_shift_id' => 'required_if:type,==,' . ConsultationTypeConstants::WITH_APPOINTMENT->value . '|' . sprintf(config('validations.model.null'), 'doctor_schedule_day_shifts', 'id'),

                'contact_type' => config('validations.integer.null') . '|in:' . implode(',', ConsultationContactTypeConstants::values()),

                // 'reminder_before' => 'required_if:type,==,' . ConsultationTypeConstants::WITH_APPOINTMENT->value . '|' . config('validations.integer.null') . '|in:' . implode(',', ReminderConstants::values()), // removed

                'payment_type' => config('validations.integer.req') . '|in:' . implode(',', ConsultationPaymentTypeConstants::values()),
                'medical_speciality_id' => sprintf(config('validations.model.active_null'), 'medical_specialities'),
                'coupon_code' => ['nullable', 'exists:coupons,code', new ValidCouponRule()]
            ];
        } else {
            return [
                'patient_description' => config('validations.text.null'),
                'attachments' => config('validations.array.null'),
                'attachments.*' => sprintf(config('validations.model.req'), 'files'),

                'questions' => 'nullable|array',
                'questions.*.consultation_question_id' => 'required|exists:consultation_questions,id|distinct',
                'questions.*.answer' => 'required|string',
            ];
        }

        // return [
        //     'patient_id' => sprintf(config('validations.model.active_null'), 'patients'),
        //     'doctor_id' => sprintf(config('validations.model.active_null'), 'doctors') . '|required_if:type,==,' . ConsultationTypeConstants::WITH_APPOINTMENT->value,

        //     // remove from here
        //     'patient_description' => config('validations.text.null'),
        //     'attachments' => config('validations.array.null'),
        //     'attachments.*' => sprintf(config('validations.model.req'), 'files'),

        //     'type' => config('validations.integer.req') . '|in:' . implode(',', ConsultationTypeConstants::values()),
        //     'doctor_schedule_day_shift_id' => 'required_if:type,==,' . ConsultationTypeConstants::WITH_APPOINTMENT->value . '|' . sprintf(config('validations.model.null'), 'doctor_schedule_day_shifts', 'id'),

        //     'contact_type' => config('validations.integer.null') . '|required_if:type,==,' . ConsultationTypeConstants::URGENT->value . '|in:' . implode(',', ConsultationContactTypeConstants::values()),

        //     // 'reminder_before' => 'required_if:type,==,' . ConsultationTypeConstants::WITH_APPOINTMENT->value . '|' . config('validations.integer.null') . '|in:' . implode(',', ReminderConstants::values()), // removed

        //     'payment_type' => config('validations.integer.req') . '|in:' . implode(',', ConsultationPaymentTypeConstants::values()),
        //     'medical_speciality_id' => sprintf(config('validations.model.active_null'), 'medical_specialities'),
        //     'coupon_code' => ['nullable', 'exists:coupons,code', new ValidCouponRule()]
        // ];
    }

    /**
     * Customizing input names displayed for user
     * @return array
     */
    public function attributes(): array
    {
        return [
            'patient_id' => __('messages.patient'),
            'doctor_id' => __('messages.doctor'),
            'patient_description' => __('messages.patient_description'),
            'attachments' => __('messages.attachments'),
            'type' => __('messages.type'),
            'doctor_schedule_day_shift_id' => __('messages.doctorScheduleDayShift'),
            'contact_type' => __('messages.contact_type'),
            'reminder_before' => __('messages.reminder_before'),
            'payment_type' => __('messages.payment_type'),
            'medical_speciality_id' => __('messages.medicalSpeciality'),
            'coupon_code' => __('messages.coupon_code'),
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [];
    }
}
