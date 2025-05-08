<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\PackageSubscribeRequest;
use App\Http\Resources\DoctorPackageResource;
use App\Models\Consultation;
use App\Models\Package;
use App\Repositories\Contracts\ConsultationContract;
use App\Repositories\Contracts\PackageContract;
use App\Repositories\Contracts\SubscriptionContract;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class PatientPackageController extends BaseApiController
{
    public SubscriptionContract $subscriptionContract;

    public function __construct(PackageContract $contract, SubscriptionContract $subscriptionContract)
    {
        $this->contract             = $contract;
        $this->subscriptionContract = $subscriptionContract;

        $this->defaultScopes        = ['active' => true];
        $this->relations            = ['image', 'user', 'subscriptions', 'consultations', 'consultations.doctor', 'consultations.doctor.specializations'];

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
            $package      = Package::findOrFail(request('package'));
            $subscription = $this->subscriptionContract->create(Arr::except($request->validated(), ['doctor_id']) + ['doctor_id' => $package->user_id]);
            resolve(ConsultationContract::class)->create($request->validated(), [
                'package_id' => $subscription->package_id,
                'subscription_id' => $subscription->id,
            ]);
            return $this->respondWithSuccess('Subscribed successfully', ['subscription' => $subscription, 'consultations' => $subscription->package?->consultations]);
        } catch (Exception $e) {
            info($e);
            return $this->respondWithError($e->getMessage());
        }
    }
}
