<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\AssignEducationalContentRequest;
use App\Http\Requests\EducationalContentFilterRequest;
use App\Http\Resources\EducationalContentResource;
use App\Models\Consultation;
use App\Repositories\Contracts\EducationalContentContract;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class EducationalContentController extends BaseApiController
{
    protected array $relations = ['author', 'medicalSpeciality', 'mainImage'];

    public function __construct(EducationalContentContract $contract)
    {
        parent::__construct($contract, EducationalContentResource::class);
    }

    public function getAll(EducationalContentFilterRequest $request)
    {
        try {
            $filters = $request->validated();
            $filters['locale'] = $request->header('X-localization', config('app.locale', 'en'));
            $contents = $this->contract->getAll($filters, $this->relations);
            return $this->respondWithCollection($contents);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
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
    
    public function assignToConsultation(Consultation $consultation, AssignEducationalContentRequest $request)
    {
        try {
            $doctor = auth()->user()->doctor;
            if (!$doctor) {
                return $this->respondWithError('غير مصرح: لست دكتورًا', Response::HTTP_FORBIDDEN);
            }

            $success = $this->contract->assignToConsultation($consultation, $request->validated()['content_ids'], $doctor->id);
            if (!$success) {
                return $this->respondWithError('غير مصرح: هذه الاستشارة لا تخصك', Response::HTTP_FORBIDDEN);
            }

            return $this->respondWithArray(['message' => 'تم الإضافة بنجاح'], [], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function removeFromConsultation(Consultation $consultation, AssignEducationalContentRequest $request)
    {
        try {
            $doctor = auth()->user()->doctor;
            if (!$doctor) {
                return $this->respondWithError('غير مصرح: لست دكتورًا', Response::HTTP_FORBIDDEN);
            }

            $success = $this->contract->removeFromConsultation($consultation, $request->validated()['content_ids'], $doctor->id);
            if (!$success) {
                return $this->respondWithError('غير مصرح: هذه الاستشارة لا تخصك', Response::HTTP_FORBIDDEN);
            }

            return $this->respondWithArray(['message' => 'تم الحذف بنجاح'], [], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getByConsultation(Consultation $consultation)
    {
        try {
            // if (!$consultation->isMineAsDoctor() && !$consultation->isMineAsPatient()) {
            //     return $this->respondWithError('غير مصرح: ليس لديك صلاحية للوصول إلى هذه الاستشارة', Response::HTTP_FORBIDDEN);
            // }

            $contents = $this->contract->getByConsultation($consultation, $this->relations);
            return $this->respondWithCollection($contents);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

}