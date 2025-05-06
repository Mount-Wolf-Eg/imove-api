<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\PackageSubscribeRequest;
use App\Http\Resources\DoctorPackageResource;
use App\Models\Package;
use App\Repositories\Contracts\PackageContract;
use App\Repositories\Contracts\SubscriptionContract;
use Exception;
use Illuminate\Http\JsonResponse;

class PatientPackageController extends BaseApiController
{
    public SubscriptionContract $subscriptionContract;

    public function __construct(PackageContract $contract, SubscriptionContract $subscriptionContract)
    {
        $this->contract             = $contract;
        $this->subscriptionContract = $subscriptionContract;

        $this->defaultScopes        = ['active' => true];

        parent::__construct($contract, DoctorPackageResource::class);
    }

    /**
     * Display the specified resource.
     * @param Package $package
     * @return JsonResponse
     */
    public function show(Package $package): JsonResponse
    {
        try {
            return $this->respondWithModel($package->load($this->relations));
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

    public function subscribe(PackageSubscribeRequest $request): JsonResponse
    {
        try {
            $subscription = $this->subscriptionContract->create($request->validated());
            return $this->respondWithSuccess('Subscribed successfully', ['subscription' => $subscription]);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }
}
