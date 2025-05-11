<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\SessionAnalyticsResource;
use App\Repositories\Contracts\ProgramContract;
use App\Models\{Program, PatientSession};
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class doctorProgramController extends BaseApiController
{
    protected array $relations = ['consultation.doctor.medicalSpecialities', 'exercises', 'patientSessions'];

    /**
     * PatientProgramController constructor.
     * @param ProgramContract $contract
     */
    public function __construct(ProgramContract $contract)
    {
        parent::__construct($contract, ProgramResource::class);
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