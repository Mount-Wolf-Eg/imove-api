<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Constants\ConsultationStatusConstants;
use App\Constants\ConsultationTypeConstants;
use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\CreateOrUpdateSettingProgramRequest;
use App\Http\Requests\CreateProgramExerciseRequest;
use App\Http\Requests\ExercisesIdRequest;
use App\Http\Requests\DiagnosisRequest;
use App\Http\Resources\ConsultationResource;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\ProgramExercisesResource;
use App\Http\Resources\SettingProgramExercisesResource;
use App\Models\Consultation;
use App\Repositories\Contracts\ConsultationContract;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class DoctorExerciseController extends BaseApiController
{

    /**
     * PatientConsultationController constructor.
     * @param ConsultationContract $contract
     */
    public function __construct(
        ConsultationContract        $contract,
    ) {
        $this->middleware('role:doctor');
        parent::__construct($contract, SettingProgramExercisesResource::class);
    }


    // get Program Exercises in consultation
    public function getProgramExercises(Consultation $consultation): JsonResponse
    {
        try {
            return $this->respondWithResource(new ProgramExercisesResource($consultation->program), 201);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), $e->getCode() ?: 422);
        }
    }

    // get Setting Program Exercises in consultation
    public function getSettingProgramExercises(Consultation $consultation)
    {
        try {
            $program = $consultation->program ?? null ;
            return $this->respondWithResource(new SettingProgramExercisesResource($program), 201);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), $e->getCode() ?: 422);
        }
    }

    // Update Or Create Setting Program Exercises
    public function updateOrCreateSettingProgram(CreateOrUpdateSettingProgramRequest $request, Consultation $consultation)
    {
        try {
            $program = $this->contract->updateOrCreateSettingProgram(
                $consultation, $request->validated()
            );

            return $this->respondWithModel($program);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), $e->getCode() ?: 422);
        }
    }

    // assign To (add) Program Exercises consultation
    public function assignToProgramExercises(CreateProgramExerciseRequest $request, Consultation $consultation)
    {
        try {
            // return $request->validated();
            return $this->contract->createProgramExercises(
                $consultation, $request->validated()
            );

            return $this->respondWithArray(['message' => __('messages.create_success')], [], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    // remove From Program Exercises consultation
    public function removeFromProgramExercises(Consultation $consultation, ExercisesIdRequest $request)
    {
        try {
            $this->contract->deleteProgramExercises(
                $consultation, $request->validated()['exercise_ids']
            );

            return $this->respondWithArray(['message' => __('messages.delete_success')], [], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }


    // Update Or Create Diagnosis consultation
    public function updateOrCreateDiagnosis(DiagnosisRequest $request, Consultation $consultation)
    {
        try {
            $program = $this->contract->updateOrCreateDiagnosis(
                $consultation, $request->validated()
            );

            dd($program);

            return $this->respondWithModel($program);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), $e->getCode() ?: 422);
        }
    }

}
