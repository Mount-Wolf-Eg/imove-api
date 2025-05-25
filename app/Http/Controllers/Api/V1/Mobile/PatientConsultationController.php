<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Constants\ConsultationStatusConstants;
use App\Constants\FileConstants;
use App\Constants\ConsultationPatientStatusConstants;
use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\ConsultationRequest;
use App\Http\Requests\PatientUrgentApproveRequest;
use App\Http\Requests\PatientUrgentRejectRequest;
use App\Http\Resources\ConsultationResource;
use App\Http\Resources\FileResource;
use App\Models\Consultation;
use App\Models\File;
use App\Repositories\Contracts\ConsultationContract;
use App\Repositories\Contracts\DoctorContract;
use App\Services\Repositories\ConsultationNotificationService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;

class PatientConsultationController extends BaseApiController
{
    private ConsultationNotificationService $notificationService;

    /**
     * PatientConsultationController constructor.
     * @param ConsultationContract $contract
     * @param ConsultationNotificationService $notificationService
     */
    public function __construct(ConsultationContract $contract, ConsultationNotificationService $notificationService)
    {
        $this->defaultScopes = ['mineAsPatient' => true];
        $this->relations = ['patient', 'doctorScheduleDayShift.day', 'doctor.rates', 'medicalSpeciality', 'replies'];
        parent::__construct($contract, ConsultationResource::class);
        $this->notificationService = $notificationService;
    }

    /**
     * Store a newly created resource in storage.
     * @param ConsultationRequest $request
     * @return JsonResponse
     */
    public function store(ConsultationRequest $request): JsonResponse
    {
        try {
            $consultation = $this->contract->create($request->validated());
            $this->relations[] = 'attachments';
            return $this->respondWithModel($consultation);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * @param Consultation $consultation
     * @return JsonResponse
     */
    public function show(Consultation $consultation): JsonResponse
    {
        try {
            if (!$consultation->isMineAsPatient())
                abort(422, __('messages.not_allowed'));
            $this->relations = array_merge($this->relations, ['attachments', 'vendors', 'patient.diseases']);
            return $this->respondWithModel($consultation);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ConsultationRequest $request
     * @param Consultation $consultation
     * @return JsonResponse
     */
    public function update(ConsultationRequest $request, $consultation_id): JsonResponse
    {
        try {
            $consultation = Consultation::withoutGlobalScopes()->findOrFail($consultation_id);
            $consultation = $this->contract->update($consultation, $request->validated());
            return $this->respondWithModel($consultation);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param Consultation $consultation
     * @return JsonResponse
     */
    public function destroy(Consultation $consultation): JsonResponse
    {
        try {
            $this->contract->remove($consultation);
            return $this->respondWithSuccess(__('messages.deleted'));
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * active & inactive the specified resource from storage.
     * @param Consultation $consultation
     * @return JsonResponse
     */
    public function changeActivation(Consultation $consultation): JsonResponse
    {
        try {
            $this->contract->toggleField($consultation, 'is_active');
            return $this->respondWithModel($consultation);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * Cancel the specified resource from storage.
     * @param Consultation $consultation
     * @return JsonResponse
     */
    public function cancel(Consultation $consultation): JsonResponse
    {
        if (!$consultation->patientCanCancel() && ! request()->confirmed) {
            return response()->json(['data' => ['need_to_confirm' => true], 'message' => __('messages.patient_can_not_cancel_consultation')], 422);
        }

        try {
            $consultation = $this->contract->update($consultation, ['status' => ConsultationStatusConstants::PATIENT_CANCELLED->value]);

            if ($consultation->subscribe && $consultation->subscribe->ofAvailable) {
                if ($consultation->returnMony()) {
                    $consultation->subscribe()->decrement('used_num_of_sessions', 1);
                    $consultation->update(['is_replaceable' => true]);
                } else {
                    $consultation->update(['is_replaceable' => false]);
                }
            } else {
                if ($consultation->returnMony()) $this->contract->refundAmount($consultation, $consultation->total_amount);
            }

            $this->notificationService->patientCancel($consultation);
            return $this->respondWithModel($consultation);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * Confirm referral
     * @param Consultation $consultation
     * @return JsonResponse
     */
    public function confirmReferral(Consultation $consultation): JsonResponse
    {
        if (!$consultation->patientCanConfirmReferral())
            abort(422, __('messages.patient_can_not_confirm_referral'));
        try {
            $consultation = $this->contract->update($consultation, ['status' => ConsultationStatusConstants::PATIENT_CONFIRM_REFERRAL->value]);
            $this->notificationService->patientCancel($consultation);
            return $this->respondWithModel($consultation);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * get urgent doctor replies
     *
     * @return JsonResponse
     */
    public function replies(): JsonResponse
    {
        request()->merge(['scope' => 'full']);
        try {
            $filters = [
                'urgentWithNoDoctor' => true,
                'medicalSpeciality' => request('medicalSpeciality'),
                'patient' => request('patient') ?? auth()->user()->patient?->id
            ];
            $consultation = $this->contract->findByFilters($filters, ['replies.rates', 'patient', 'medicalSpeciality'], false);
            if (!$consultation)
                return $this->respondWithSuccess(__('messages.no_data'));
            if (request('orderBy') == 'topRated') {
                $consultation->replies = $consultation->replies->sortByDesc(function ($reply) {
                    return $reply->rates->avg('value');
                });
            } elseif (request('orderBy') == 'highestPrice') {
                $consultation->replies = $consultation->replies->sortBy('amount')->reverse();
            } elseif (request('orderBy') == 'lowestPrice') {
                $consultation->replies = $consultation->replies->sortBy('amount');
            }
            $this->relations = ['replies.rates', 'medicalSpeciality'];
            return $this->respondWithModel($consultation);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * approve urgent doctor offer
     * @param PatientUrgentApproveRequest $request
     * @param Consultation $consultation
     * @return JsonResponse
     */
    public function approveUrgentDoctorOffer(PatientUrgentApproveRequest $request, Consultation $consultation)
    {
        try {
            $data = $request->validated();
            $consultation = $this->contract->update($consultation, [
                'doctor_id' => $data['doctor_id'],
                'amount' => $data['amount'],
                'status' => ConsultationStatusConstants::URGENT_PATIENT_APPROVE_DOCTOR_OFFER->value,
                'is_active' => false
            ]);
            $this->contract->syncWithoutDetaching($consultation, 'replies', $data['replies']);
            $this->notificationService->patientAcceptDoctorOffer($consultation);
            return $this->respondWithModel($consultation);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * reject urgent doctor offer
     * @param PatientUrgentRejectRequest $request
     * @param Consultation $consultation
     * @return JsonResponse
     */
    public function rejectUrgentDoctorOffer(PatientUrgentRejectRequest $request, Consultation $consultation)
    {
        try {
            $data = $request->validated();
            $doctor = resolve(DoctorContract::class)->find($data['doctor_id']);
            $this->contract->syncWithoutDetaching($consultation, 'replies', $data['replies']);
            $this->notificationService->patientRejectDoctorOffer($consultation, $doctor);
            return $this->respondWithModel($consultation);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

    public function files()
    {
        $attachments = File::where('type', FileConstants::FILE_TYPE_CONSULTATION_ATTACHMENTS)
            ->whereHas('consultation', function ($query) {
                $query->where('patient_id', auth()->user()->patient?->id);
            })
            ->get();

        return response()->json([
            'status' => 200,
            'files'  => FileResource::collection($attachments),
        ]);
    }

    
    /**
     * patient_start_at the consultation.
     * @param Consultation $consultation
     * @return JsonResponse
     */
    public function patientStartAt(Consultation $consultation)
    {
        try {
            if (!$consultation->patient_start_at) {
                $consultation = $this->contract->update($consultation, [
                    'patient_start_at' => now(), 
                ]);
            }
            return $this->respondWithSuccess();
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), 422);
        }
    }


    /**
     * Get the next appointment date for the authenticated patient.
     *
     * @return JsonResponse
     */
    public function nextAppointment(): JsonResponse
    {
        try {
            $patientId = auth()->user()->patient?->id;

            if (!$patientId) {
                return $this->respondWithError(__('messages.errors.no_patient_account'), 404);
            }

            $consultation = Consultation::where('patient_id', $patientId)
                ->ofNextConsultation()
                ->select('id', 'doctor_schedule_day_shift_id')
                ->with(['doctorScheduleDayShift.day', 'replies' => function ($query) {
                    $query->where('status', ConsultationPatientStatusConstants::APPROVED->value)
                        ->select('consultation_doctor_replies.id', 'consultation_doctor_replies.consultation_id', 'consultation_doctor_replies.doctor_set_consultation_at');
                }])
                ->orderByRaw('
                    COALESCE(
                        (
                            SELECT STR_TO_DATE(
                                CONCAT(
                                    doctor_schedule_days.date, 
                                    " ", 
                                    doctor_schedule_day_shifts.from_time
                                ), 
                                "%Y-%m-%d %H:%i:%s"
                            )
                            FROM doctor_schedule_day_shifts
                            JOIN doctor_schedule_days 
                                ON doctor_schedule_days.id = doctor_schedule_day_shifts.doctor_schedule_day_id
                            WHERE doctor_schedule_day_shifts.id = consultations.doctor_schedule_day_shift_id
                        ),
                        (
                            SELECT doctor_set_consultation_at
                            FROM consultation_doctor_replies
                            WHERE consultation_doctor_replies.consultation_id = consultations.id
                            AND consultation_doctor_replies.status = ?
                            LIMIT 1
                        )
                    ) ASC
                ', [ConsultationPatientStatusConstants::APPROVED->value])
                ->first();

            if (!$consultation) {
                return $this->respondWithSuccess(__('messages.no_data'), 200, [
                    'consultation_id' => null,
                    'day_next_appointment' => null,
                    'date_next_appointment' => null,
                    'time_next_appointment' => null
                ]);
            }

            $nextAppointment = null;
            if ($consultation->doctorScheduleDayShift?->day) {
                $shiftDateTime = $consultation->doctorScheduleDayShift->day->date
                    ->copy()
                    ->setTimeFrom($consultation->doctorScheduleDayShift->from_time);
                if ($shiftDateTime->isFuture()) {
                    $nextAppointment = $shiftDateTime;
                }
            }
            $approvedReply = $consultation->replies->first();
            if ($approvedReply?->doctor_set_consultation_at && $approvedReply->doctor_set_consultation_at->isFuture()) {
                if (!$nextAppointment || $approvedReply->doctor_set_consultation_at->lessThan($nextAppointment)) {
                    $nextAppointment = $approvedReply->doctor_set_consultation_at;
                }
            }

            if (!$nextAppointment) {
                return $this->respondWithSuccess(__('messages.no_data'), 200, [
                    'consultation_id' => $consultation->id,
                    'day_next_appointment' => null,
                    'date_next_appointment' => null,
                    'time_next_appointment' => null
                ]);
            }

            $locale = app()->getLocale(); 
            return $this->respondWithSuccess(__('messages.next_appointment_found'), [
                'consultation_id' => $consultation->id?? null,
                // 'day_next_appointment' => $this->getDayName($nextAppointment)?? null,
                'day_next_appointment' => $nextAppointment->locale($locale)->dayName?? null,
                'date_next_appointment' => $nextAppointment->format('Y-m-d')?? null,
                'time_next_appointment' => $nextAppointment->format('H:i')?? null,
                'patient_can_create_general_consultation' => Consultation::patientCanCreateNewGeneralSession(auth()->user()),
            ]);
        } catch (Exception $e) {
            \Log::error('Failed to fetch next appointment', [
                'user_id' => auth()->user()->id,
                'error' => $e->getMessage(),
            ]);
            return $this->respondWithError(__('messages.error_fetching_appointment'), 500);
        }
    }

    /**
     * Get day name in Arabic/English format (e.g., "الجمعة / Friday").
     *
     * @param Carbon $date
     * @return string
     */
    // private function getDayName(Carbon $date): string
    // {
    //     $dayNames = [
    //         0 => ['ar' => 'الأحد', 'en' => 'Sunday'],
    //         1 => ['ar' => 'الإثنين', 'en' => 'Monday'],
    //         2 => ['ar' => 'الثلاثاء', 'en' => 'Tuesday'],
    //         3 => ['ar' => 'الأربعاء', 'en' => 'Wednesday'],
    //         4 => ['ar' => 'الخميس', 'en' => 'Thursday'],
    //         5 => ['ar' => 'الجمعة', 'en' => 'Friday'],
    //         6 => ['ar' => 'السبت', 'en' => 'Saturday'],
    //     ];

    //     $dayIndex = $date->dayOfWeek;
    //     return "{$dayNames[$dayIndex]['ar']} / {$dayNames[$dayIndex]['en']}";
    // }

}

