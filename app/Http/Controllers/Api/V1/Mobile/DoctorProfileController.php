<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\DoctorProfessionalStatusRequest;
use App\Http\Requests\DoctorProfileRequest;
use App\Http\Requests\DoctorScheduleRequest;
use App\Http\Requests\DoctorUniversityRequest;
use App\Http\Resources\UserResource;
use App\Models\DoctorUniversity;
use App\Repositories\Contracts\DoctorContract;
use Illuminate\Http\JsonResponse;
use App\Models\Doctor;
use App\Exceptions\CantDeleteModelException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;



class DoctorProfileController extends BaseApiController
{

    /**
     * DoctorProfileController constructor.
     * @param DoctorContract $contract
     */
    public function __construct(DoctorContract $contract)
    {
        parent::__construct($contract, UserResource::class);
    }

    /**
     * @param DoctorProfileRequest $request
     * @return JsonResponse
     */
    public function updateMainInfo(DoctorProfileRequest $request)
    {
        $doctor = auth()->user()->doctor;
        $doctor = $this->contract->update($doctor, $request->validated());
        $user = $doctor->user->load('doctor');
        return $this->respondWithModel($user);
    }

    /**
     * @param DoctorProfessionalStatusRequest $request
     * @return JsonResponse
     */
    public function updateProfessionalStatus(DoctorProfessionalStatusRequest $request)
    {
        $doctor = auth()->user()->doctor;
        $doctor = $this->contract->update($doctor, $request->validated());
        $user = $doctor->user->load('doctor.universities.university', 'doctor.hospitals',
            'doctor.universities.academicDegree', 'doctor.universities.certificate',
            'doctor.universities.medicalSpeciality');
        return $this->respondWithModel($user);
    }

    public function updateSchedule(DoctorScheduleRequest $request)
    {
        $doctor = auth()->user()->doctor;
        $doctor = $this->contract->update($doctor, $request->validated());
        $user = $doctor->user->load('doctor.scheduleDays.shifts.availableSlots');
        return $this->respondWithModel($user);
    }

    public function addUniversity(DoctorUniversityRequest $request)
    {
        $doctor = auth()->user()->doctor;
        $doctor = $this->contract->update($doctor, $request->validated());
        $user = $doctor->user->load('doctor.universities.university', 'doctor.hospitals',
            'doctor.universities.academicDegree', 'doctor.universities.certificate',
            'doctor.universities.medicalSpeciality');
        return $this->respondWithModel($user);
    }

    public function updateUniversity(DoctorUniversityRequest $request, DoctorUniversity $university)
    {
        $doctor = auth()->user()->doctor;
        $doctor = $this->contract->update($doctor, $request->validated());
        $user = $doctor->user->load('doctor.universities.university', 'doctor.hospitals',
            'doctor.universities.academicDegree', 'doctor.universities.certificate',
            'doctor.universities.medicalSpeciality');
        return $this->respondWithModel($user);
    }

    public function deleteUniversity(DoctorUniversity $university)
    {
        $doctor = auth()->user()->doctor;
        $doctor->universities()->delete($university);
        $user = $doctor->user->load('doctor.universities.university', 'doctor.hospitals',
            'doctor.universities.academicDegree', 'doctor.universities.certificate',
            'doctor.universities.medicalSpeciality');
        return $this->respondWithModel($user);
    }

    public function deactivate()
    {
        $doctor = auth()->user()->doctor;
        $this->contract->toggleField($doctor, 'is_active');
        return $this->respondWithSuccess(__('messages.actions_messages.update_success'));
    }

    
    /**
     * Delete the authenticated doctor's account.
     *
     * @return JsonResponse
     */
    public function deleteAccount(): JsonResponse
    {
        $user = auth()->user();
        $doctor = $user->doctor;
        
        try {
            $this->doctorContract->remove($doctor);
            return $this->respondWithSuccess(__('messages.actions_messages.delete_success'));
        } catch (CantDeleteModelException $e) {
            return $this->respondWithError($e->getMessage(), 422);
        } catch (\Exception $e) {
            \Log::error('Failed to delete doctor account via API', [
                'doctor_id' => $doctor->id, 'user_id' => $user->id, 'error' => $e->getMessage(),
            ]);
            return $this->respondWithError(__('messages.actions_messages.delete_failed'), 500);
        }
    }

}
