<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\BankRequest;
use App\Http\Resources\BankResource;
use App\Models\Bank;
use App\Repositories\Contracts\BankContract;
use Exception;
use \Illuminate\Http\JsonResponse;

class BankController extends BaseApiController
{
    /**
     * BankController constructor.
     * @param BankContract $contract
     */
    public function __construct(BankContract $contract)
    {
        $this->defaultScopes = ['auth' => true];
        parent::__construct($contract, BankResource::class);
    }

    /**
     * Store a newly created resource in storage.
     * @param BankRequest $request
     * @return JsonResponse
     */
    public function store(BankRequest $request): JsonResponse
    {
        try {
            $bank = $this->contract->create($request->validated());
            return $this->respondWithModel($bank->load($this->relations));
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }
    /**
     * Display the specified resource.
     * @param Bank $bank
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $bank = $this->contract->findOrFail($id, [], ['auth' => true]);
        try {
            return $this->respondWithModel($bank->load($this->relations));
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param BankRequest $request
     * @param Bank $bank
     * @return JsonResponse
     */
    public function update(BankRequest $request, $id): JsonResponse
    {
        $bank = $this->contract->findOrFail($id, [], ['auth' => true]);
        try {
            $bank = $this->contract->update($bank, $request->validated());
            return $this->respondWithModel($bank->load($this->relations));
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }
    /**
     * Remove the specified resource from storage.
     * @param Bank $bank
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $bank = $this->contract->findOrFail($id, [], ['auth' => true]);
        try {
            $this->contract->remove($bank);
            return $this->respondWithSuccess(__('messages.deleted'));
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * active & inactive the specified resource from storage.
     * @param Bank $bank
     * @return JsonResponse
     */
    public function changeActivation($id): JsonResponse
    {
        $bank = $this->contract->findOrFail($id, [], ['auth' => true]);
        try {
            $this->contract->toggleField($bank, 'is_active');
            return $this->respondWithModel($bank->load($this->relations));
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }
}
