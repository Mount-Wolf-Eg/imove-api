<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Constants\DoctorRequestStatusConstants;
use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\PatientResource;
use App\Models\Doctor;
use App\Models\GeneralSettings;
use App\Repositories\Contracts\DoctorContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorController extends BaseApiController
{
    /**
     * DoctorController constructor.
     * @param DoctorContract $contract
     */
    public function __construct(DoctorContract $contract)
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
            'universities.certificate'
        ];
    }

    public function index(array $additional = []): mixed
    {
        return parent::index(['general_session_price' => GeneralSettings::getSettingValue('general_session_price')] + $additional);
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
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $order = $request->input('order', []);

            $filters = [];
            $data = array_merge($filters, [
                'order' => $order,
                'limit' => $limit,
                'page' => $page,
            ]); 

            $patients = $this->contract->getPatients($doctor, $nameFilter, $filters);

            return $this->respondWithCollection(PatientResource::collection($patients), 200);
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve patients: ' . $e->getMessage());
            return $this->respondWithError($e->getMessage(), $e->getCode() ?: 422);
        }
    }

}
