<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\CreateSessionRequest;
use App\Http\Requests\UpdateExerciseProgressRequest;
use App\Http\Requests\UpdateReasonForOvertakingRequest;
use App\Http\Requests\UpdateSessionRequest;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\ProgramDetailsResource;
use App\Http\Resources\ProgramListResource;
use App\Http\Resources\SessionResource;
use App\Http\Resources\SessionAnalyticsResource;
use App\Repositories\Contracts\ProgramContract;
use App\Models\{Program, PatientSession};
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class PatientProgramController extends BaseApiController
{
    protected array $relations = ['consultation.doctor.medicalSpecialities', 'exercises', 'patientSessions'];

    /**
     * PatientProgramController constructor.
     * @param ProgramContract $contract
     */
    public function __construct(ProgramContract $contract)
    {
        parent::__construct($contract, ProgramListResource::class);
    }


    public function AllProgramsPatient(): JsonResponse
    {
        try {
            $filters = [
                'patient_id' => auth()->user()->patient->id,
            ];

            $programs = $this->contract->searchPatient($filters, $this->relations);
            return $this->respondWithCollection($programs);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

    public function show(Program $program): JsonResponse
    {
        try {
            if ($program->patient_id !== auth()->user()->patient->id) {
                return $this->respondWithError(__('messages.unauthorized'), 403);
            }
            // return auth()->user()->patient;
            return $this->respondWithResource(new ProgramDetailsResource($program));
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), $e->getCode() ?: 422);
        }
    }

    /**
     * Create a new session for the specified program.
     *
     * @param CreateSessionRequest $request
     * @param Program $program
     * @return JsonResponse
     */
    public function createSession(CreateSessionRequest $request, Program $program): JsonResponse
    {
        try {
            if ($program->patient_id !== auth()->user()->patient->id) {
                return $this->respondWithError(__('messages.unauthorized'), 403);
            }

            $session = $this->contract->createSession($program, $request->validated());

            $relations = ['program', 'consultation', 'sessionExercises'];
            $session->load(array_intersect($relations));
            
            return $this->respondWithResource(new SessionResource($session), 201);
        } catch (Exception $e) {
            $statusCode = (int) $e->getCode();
            $statusCode = in_array($statusCode, [200, 201, 400, 401, 403, 404, 422, 500]) ? $statusCode : 422;
            return $this->respondWithError($e->getMessage(), $statusCode);
        }
    }


    /**
     * Update exercise progress for a specific PatientSessionExercise.
     *
     * @param UpdateExerciseProgressRequest $request
     * @param int $exerciseId
     * @return JsonResponse
     */
    public function updateExerciseProgress(UpdateExerciseProgressRequest $request, int $exerciseId): JsonResponse
    {
        try {
            $exercise = $this->contract->updateExerciseProgress($exerciseId, $request->validated());
            $exercise->load(['program', 'session', 'exercise.media']);
            return $this->respondWithResource(new SessionResource($exercise->session));
        } catch (Exception $e) {
            $statusCode = (int) $e->getCode();
            $statusCode = in_array($statusCode, [200, 201, 400, 401, 403, 404, 422, 500]) ? $statusCode : 422;
            return $this->respondWithError($e->getMessage(), $statusCode);
        }
    }

    /**
     * Update/add Exercise reason for overtaking for a specific PatientSessionExercise.
     *
     * @param UpdateReasonForOvertakingRequest $request
     * @param int $exerciseId
     * @return JsonResponse
     */
    public function updateReasonForOvertaking(UpdateReasonForOvertakingRequest $request, int $exerciseId): JsonResponse
    {
        try {
            $exercise = $this->contract->updateReasonForOvertaking($exerciseId, $request->validated()['reason_for_overtaking']);
            $exercise->load(['program', 'session', 'exercise.media']);
            return $this->respondWithResource(new SessionResource($exercise->session));
        } catch (Exception $e) {
            $statusCode = (int) $e->getCode();
            $statusCode = in_array($statusCode, [200, 201, 400, 401, 403, 404, 422, 500]) ? $statusCode : 422;
            return $this->respondWithError($e->getMessage(), $statusCode);
        }
    }


    /**
     * Update session details for a specific PatientSession.
     *
     * @param UpdateSessionRequest $request
     * @param int $sessionId
     * @return JsonResponse
     */
    public function endSession(UpdateSessionRequest $request, int $sessionId): JsonResponse
    {
        try {
            $session = $this->contract->updateSession($sessionId, $request->validated());
            $program = $session->program;
            $program->load(['consultation', 'exercises', 'patientSessions']);
            return $this->respondWithResource(new ProgramDetailsResource($program));
        } catch (Exception $e) {
            $statusCode = (int) $e->getCode();
            $statusCode = in_array($statusCode, [200, 201, 400, 401, 403, 404, 422, 500]) ? $statusCode : 422;
            return $this->respondWithError($e->getMessage(), $statusCode);
        }
    }


    /**
     * Get analytical report for a specific program.
     *
     * @param Program $program
     * @return JsonResponse
     */
    public function getProgramReport(Program $program): JsonResponse
    {
        try {
            $report = $this->contract->patientAnalyzeProgram($program);
            return $this->respondWithSuccess(null, $report);
        } catch (Exception $e) {
            $statusCode = (int) $e->getCode();
            $statusCode = in_array($statusCode, [200, 201, 400, 401, 403, 404, 422, 500]) ? $statusCode : 422;
            return $this->respondWithError($e->getMessage(), $statusCode);
        }
    }

    /**
     * Get analytical report for a specific session.
     *
     * @param int $sessionId
     * @return JsonResponse
     */
    public function getSessionAnalytics(int $sessionId): JsonResponse
    {
        try {
            $analytics = $this->contract->patientSessionAnalytics($sessionId);
            return $this->respondWithResource(new SessionAnalyticsResource($analytics));
        } catch (Exception $e) {
            $statusCode = (int) $e->getCode();
            $statusCode = in_array($statusCode, [200, 201, 400, 401, 403, 404, 422, 500]) ? $statusCode : 422;
            return $this->respondWithError($e->getMessage(), $statusCode);
        }
    }

}