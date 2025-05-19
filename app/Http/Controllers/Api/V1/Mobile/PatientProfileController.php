<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\PatientMedicalRecordsRequest;
use App\Http\Requests\PatientProfileRequest;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\PatientContract;
use App\Repositories\Contracts\UserContract;
use App\Models\Patient;
use App\Exceptions\CantDeleteModelException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PatientProfileController extends BaseApiController
{
    public function __construct(PatientContract $contract)
    {
        parent::__construct($contract, UserResource::class);
    }

    public function updateMainInfo(PatientProfileRequest $request)
    {
        $patient = auth()->user()->patient;
        $patient = $this->contract->update($patient, $request->validated());
        $user = $patient->user->load('patient');
        return $this->respondWithModel($user);
    }

    public function updateMedicalRecords(PatientMedicalRecordsRequest $request)
    {
        $patient = auth()->user()->patient;
        $patient = $this->contract->update($patient, $request->validated());
        $user = $patient->user->load('patient.diseases');
        return $this->respondWithModel($user);
    }

    public function deactivate()
    {
        $user = auth()->user();
        $patient = $user->patient;
        $this->contract->toggleField($patient, 'is_active');
        resolve(UserContract::class)->toggleField($user, 'is_active');
        return $this->respondWithSuccess(__('messages.actions_messages.update_success'));
    }


    /**
     * Delete the authenticated patient's account.
     *
     * @return JsonResponse
     */
    public function deleteAccount(): JsonResponse
    {
        $user = Auth::user();
        $patient = $user->patient; // Assuming Patient has a BelongsTo relationship with User

        try {
            $this->patientContract->remove($patient);
            return $this->respondWithSuccess(__('messages.actions_messages.delete_success'));
        } catch (CantDeleteModelException $e) {
            return $this->respondWithError($e->getMessage(), 422);
        } catch (\Exception $e) {
            \Log::error('Failed to delete patient account via API', [
                'patient_id' => $patient->id, 'user_id' => $user->id, 'error' => $e->getMessage(),
            ]);
            return $this->respondWithError(__('messages.actions_messages.delete_failed'), 500);
        }
    }

}
