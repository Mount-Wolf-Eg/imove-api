<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Constants\ConsultationStatusConstants;
use App\Constants\DoctorRequestStatusConstants;
use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\ConsultationResource;
use App\Http\Resources\ConsultationInDoctorResource;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\PatientResource;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\GeneralSettings;
use App\Repositories\Contracts\DoctorContract;
use App\Repositories\Contracts\ConsultationContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorController extends BaseApiController
{
    private ConsultationContract $ConsultationContract;

    /**
     * DoctorController constructor.
     * @param DoctorContract $contract
     */
    public function __construct(DoctorContract $contract, ConsultationContract $ConsultationContract)
    {
        parent::__construct($contract, DoctorResource::class);
        $this->defaultScopes = ['requestStatus' => DoctorRequestStatusConstants::APPROVED->value, 'active' => true];
        $this->relations = [
            'rates',
            'medicalSpecialities',
            'city',
            'attachments',
            'academicDegree',
            'consultations',
            'hospitals',
            'universities.academicDegree',
            'universities.medicalSpeciality',
            'universities.university',
            'universities.certificate',
        ];
        $this->ConsultationContract = $ConsultationContract;
    }

    public function index(array $additional = []): mixed
    {
        return parent::index(['general_session_price' => GeneralSettings::getSettingValue('general_session_price')] + $additional);
    }

    public function newIndex()
    {
        $doctors = Doctor::join('doctor_schedule_days as dsd', 'doctors.id', '=', 'dsd.doctor_id')
            ->join('doctor_schedule_day_shifts as dsds', 'dsd.id', '=', 'dsds.doctor_schedule_day_id')
            ->leftJoin('consultations as c', function ($join) {
                $join->on('dsds.id', '=', 'c.doctor_schedule_day_shift_id')
                    ->where('c.is_active', true)
                    ->whereNotIn('c.status', [
                        ConsultationStatusConstants::PATIENT_CANCELLED->value,
                        ConsultationStatusConstants::DOCTOR_CANCELLED->value
                    ]);
            })
            ->whereNull('c.id')
            ->whereNotNull('dsds.parent_id')
            ->where(function ($q) {
                $q->whereDate('dsd.date', '>', now()->toDateString())
                    ->orWhere(function ($subQ) {
                        $subQ->whereDate('dsd.date', now()->toDateString())
                            ->whereTime('dsds.from_time', '>=', now()->format('H:i:s'));
                    });
            })
            ->select('doctors.*', \DB::raw('MIN(dsd.date) as nearest_date'))
            ->groupBy('doctors.id')
            ->orderBy('nearest_date')
            ->paginate(10);

        return DoctorResource::collection($doctors);
    }

    /**
     *  get specified doctor full details
     *
     * @param Doctor $doctor
     * @return JsonResponse
     */
    public function show(Doctor $doctor)
    {
        if (!$doctor->request_status?->is(DoctorRequestStatusConstants::APPROVED)) {
            return $this->respondWithError('Doctor not found', 404);
        }
        return $this->respondWithModel($doctor);
    }

    /**
     * Get patients associated with the doctor.
     *
     * @param Request $request
     * @param Doctor $doctor
     * @return JsonResponse
     */
    public function getPatients(Request $request): JsonResponse
    {
        try {
            $doctor = auth()->user()?->doctor;
            $nameFilter = $request->query('name');

            $filters = [
                'limit' => $request->query('per_page', 10),
                'page'  => $request->query('page', 1),
                'order' => $request->query('pagordere', 1),
            ];
            $patients = $this->contract->getPatients($doctor, $nameFilter, $filters);

            return $this->respondWithCollection(PatientResource::collection($patients));
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve patients: ' . $e->getMessage());
            return $this->respondWithError($e->getMessage(), 422);
        }
    }

    /**
     * Get consultations for a specific patient with the doctor.
     *
     * @param Request $request
     * @param Patient $patient
     * @return JsonResponse
     */
    public function getPatientConsultationsInDoctor(Request $request, Patient $patient)
    {
        try {
            $doctor = auth()->user()?->doctor;

            $filters = [
                'limit' => $request->query('per_page', 10),
                'page' => $request->query('page', 1),
            ];

            $consultations = $this->contract->getPatientConsultations($doctor, $patient->id, $filters);

            return $this->respondWithResource(ConsultationInDoctorResource::collection($consultations));
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve consultations: ' . $e->getMessage());
            return $this->respondWithError($e->getMessage(), 422);
        }
    }
}
