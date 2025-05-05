<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\CreateSessionRequest;
use App\Http\Resources\{ProgramResource, ProgramListResource, SessionResource};
use App\Repositories\Contracts\ProgramContract;
use App\Models\Program;
use Exception;
use Illuminate\Http\JsonResponse;


class PatientProgramController extends BaseApiController
{
    protected array $relations = ['consultation', 'exercises', 'sessions'];

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
                'patient_id' => auth()->user()->patient->id ?? 0,
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

            return $this->respondWithResource(new ProgramResource($program));
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
            return $this->respondWithError($e->getMessage(), $e->getCode() ?: 422);
        }
    }
    
}