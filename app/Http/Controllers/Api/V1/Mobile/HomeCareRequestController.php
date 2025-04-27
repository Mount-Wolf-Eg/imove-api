<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\HomeCareRequestFilterRequest;
use App\Http\Requests\StoreHomeCareRequest;
use App\Http\Resources\HomeCareRequestResource;
use App\Repositories\Contracts\HomeCareRequestContract;
use App\Models\HomeCareRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HomeCareRequestController extends BaseApiController
{
    protected array $relations = ['patient', 'city', 'medicalSpeciality'];

    public function __construct(HomeCareRequestContract $contract)
    {
        parent::__construct($contract, HomeCareRequestResource::class);
    }

    public function store(StoreHomeCareRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['patient_id'] = auth()->user()->patient->id;

            $homeCareRequest = $this->contract->store($data);

            return $this->respondWithModel($homeCareRequest, Response::HTTP_CREATED);
        } catch (Exception $e) {
            \Log::error('Failed to create home care request: ' . $e->getMessage());
            return $this->respondWithError('An error occurred while creating the home care request', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function patientHomeCare(HomeCareRequestFilterRequest $request): JsonResponse
    {
        try {
            $filters = $request->validated();
            $filters['page'] = $request->input('page', 1);
            $filters['limit'] = $request->input('limit', 10);
            $filters['order'] = $request->input('order', []);

            $homeCareRequests = $this->contract->getAll($filters, $this->relations);

            return $this->respondWithCollection($homeCareRequests);
        } catch (Exception $e) {
            \Log::error('Failed to retrieve home care requests: ' . $e->getMessage());
            return $this->respondWithError('An error occurred while retrieving home care requests', Response::HTTP_BAD_REQUEST);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $homeCareRequest = $this->contract->findOne($id, $this->relations);
            return $this->respondWithModel($homeCareRequest);
        } catch (Exception $e) {
            \Log::error('Failed to retrieve home care request: ' . $e->getMessage());
            return $this->respondWithError('Home care request not found', Response::HTTP_NOT_FOUND);
        }
    }

}
