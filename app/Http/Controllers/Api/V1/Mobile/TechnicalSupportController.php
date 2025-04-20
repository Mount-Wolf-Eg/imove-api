<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\TechnicalSupportRequest;
use App\Repositories\Contracts\TechnicalSupportContract;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class TechnicalSupportController extends BaseApiController
{

    public function __construct(TechnicalSupportContract $contract)
    {
        parent::__construct($contract, null);
    }

    public function createForDoctor(TechnicalSupportRequest $request)
    {
        try {
            $doctor = auth()->user()->doctor;
            $technicalSupport = $this->contract->createForDoctor($request->validated(), $doctor->id);

            return $this->respondWithArray(['message' => __('messages.technical_support_added')], [], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function createForUser(TechnicalSupportRequest $request)
    {
        try {
            $user = auth()->user();
            $this->contract->createForUser($request->validated(), $user->id);
            // $this->contract->createForUser($request->validated(), 419);

            return $this->respondWithArray(['message' => __('messages.technical_support_added')], [], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

}