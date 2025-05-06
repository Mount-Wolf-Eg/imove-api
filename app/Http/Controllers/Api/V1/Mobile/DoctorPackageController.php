<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\DoctorPackageRequest;
use App\Http\Resources\DoctorPackageResource;
use App\Models\Package;
use App\Repositories\Contracts\PackageContract;
use Exception;
use \Illuminate\Http\JsonResponse;

class DoctorPackageController extends BaseApiController
{
    /**
     * DoctorPackageController constructor.
     * @param PackageContract $contract
     */
    public function __construct(PackageContract $contract)
    {
        $this->relations = ['user'];
        $this->defaultScopes = ['owner'];
        parent::__construct($contract, DoctorPackageResource::class);
    }

    /**
     * Store a newly created resource in storage.
     * @param DoctorPackageRequest $request
     * @return JsonResponse
     */
    public function store(DoctorPackageRequest $request): JsonResponse
    {
        try {
            $package = $this->contract->create($request->validated());
            return $this->respondWithModel($package->load($this->relations));
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
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

    /**
     * Update the specified resource in storage.
     *
     * @param DoctorPackageRequest $request
     * @param Package $package
     * @return JsonResponse
     */
    public function update(DoctorPackageRequest $request, Package $package): JsonResponse
    {
        try {
            $package = $this->contract->update($package, $request->validated());
            return $this->respondWithModel($package->load($this->relations));
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param Package $package
     * @return JsonResponse
     */
    public function destroy(Package $package): JsonResponse
    {
        try {
            $this->contract->remove($package);
            return $this->respondWithSuccess(__('messages.deleted'));
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * active & inactive the specified resource from storage.
     * @param Package $package
     * @return JsonResponse
     */
    public function changeActivation(Package $package): JsonResponse
    {
        try {
            $this->contract->toggleField($package, 'is_active');
            return $this->respondWithModel($package->load($this->relations));
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

    public function settingPackage()
    {
        try {
            $setting_package = $this->contract->getSettingPackage();
            return $this->respondWithModel($setting_package);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }
}
