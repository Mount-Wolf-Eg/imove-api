<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Resources\MedicalEquipmentResource;
use App\Models\Consultation;
use App\Repositories\Contracts\MedicalEquipmentContract;
use Exception;
use Illuminate\Http\Request;
use \Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MedicalEquipmentController extends BaseApiController
{
    protected array $relations = ['category', 'photo'];

    /**
     * MedicalEquipmentContract constructor.
     * @param MedicalEquipmentContract $contract
     */
    public function __construct(MedicalEquipmentContract $contract)
    {
        // $this->defaultScopes = ['auth' => true];
        parent::__construct($contract, MedicalEquipmentResource::class);
    }
    
    public function show($id)
    {
        try {
            $medicalEquipment = $this->contract->findOrFail($id, $this->relations);
            return $this->respondWithModel($medicalEquipment);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }


    public function assignToConsultation(Request $request, Consultation $consultation)
    {
        try {
            $validated = $request->validate([
                'medical_equipment_ids' => 'required|array',
                'medical_equipment_ids.*' => 'exists:medical_equipment,id',
            ]);

            // $doctor = 205;
            $doctor = auth()->user()->doctor;

            // $success = $this->contract->assignToConsultation($consultation, $validated['medical_equipment_ids'], 205);
            $success = $this->contract->assignToConsultation($consultation, $validated['medical_equipment_ids'], $doctor->id);

            if ($success) {
                return $this->respondWithArray(['message' => 'Medical equipment assigned successfully'], [], Response::HTTP_OK);
            }
            return $this->respondWithError('Failed to assign medical equipment', Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function removeFromConsultation(Request $request, Consultation $consultation)
    {
        try {
            $validated = $request->validate([
                'medical_equipment_ids' => 'required|array',
                'medical_equipment_ids.*' => 'exists:medical_equipment,id',
            ]);

            // $doctor = 205;
            // $success = $this->contract->removeFromConsultation($consultation, $validated['medical_equipment_ids'], 205);
            $doctor = auth()->user()->doctor;
            $success = $this->contract->removeFromConsultation($consultation, $validated['medical_equipment_ids'], $doctor->id);

            if ($success) {
                return $this->respondWithArray(['message' => 'Medical equipment removed successfully'], [], Response::HTTP_OK);
            }
            return $this->respondWithError('Failed to remove medical equipment', Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    
    public function getByConsultation(Consultation $consultation)
    {
        try {
            if (!$consultation->isMineAsDoctor() && !$consultation->isMineAsPatient()) {
                return $this->respondWithError('Unauthorized: You do not have access to this consultation', Response::HTTP_FORBIDDEN);
            }

            $medicalEquipments = $this->contract->getByConsultation($consultation, $this->relations);

            return $this->respondWithCollection($medicalEquipments);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }


}
