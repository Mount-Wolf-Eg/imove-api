<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Resources\StaticPageResource;
use App\Repositories\Contracts\StaticPageContract;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StaticPageController extends BaseApiController
{
    public function __construct(StaticPageContract $contract)
    {
        parent::__construct($contract, StaticPageResource::class);
    }

    public function getPatientTermsAndConditions(Request $request)
    {
        try {
            $page = $this->contract->getByPage('terms_and_conditions_patient');
            if (!$page) {
                return $this->respondWithError('Patient terms and conditions not found', Response::HTTP_NOT_FOUND);
            }
            return $this->respondWithModel($page);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getDoctorTermsAndConditions(Request $request)
    {
        try {
            $page = $this->contract->getByPage('terms_and_conditions_doctor');
            if (!$page) {
                return $this->respondWithError('Doctor terms and conditions not found', Response::HTTP_NOT_FOUND);
            }
            return $this->respondWithModel($page);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
    
}