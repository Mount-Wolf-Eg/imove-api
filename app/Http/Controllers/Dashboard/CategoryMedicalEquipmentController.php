<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\EquipmentDategoryRequest;
use App\Models\CategoryMedicalEquipment;
use App\Repositories\Contracts\CategoryMedicalEquipmentContract;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseWebController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryMedicalEquipmentController extends BaseWebController
{
    /**
     * CategoryMedicalEquipmentController constructor.
     * @param CategoryMedicalEquipmentContract $contract
     */
    public function __construct(CategoryMedicalEquipmentContract $contract)
    {
        parent::__construct($contract, 'dashboard');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        $resources = $this->contract->search($request->all());
        return $this->indexBlade(['resources' => $resources]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        return $this->createBlade();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EquipmentDategoryRequest $request
     *
     * @return RedirectResponse
     */
    public function store(EquipmentDategoryRequest $request): RedirectResponse
    {
        $this->contract->create($request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.create_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param CategoryMedicalEquipment $categoryMedicalEquipment
     *
     * @return View|Factory|Application
     */
    public function show(CategoryMedicalEquipment $categoryMedicalEquipment): View|Factory|Application
    {
        return $this->showBlade(['categoryMedicalEquipment' => $categoryMedicalEquipment]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CategoryMedicalEquipment $categoryMedicalEquipment
     *
     * @return View|Factory|Application
     */
    public function edit(CategoryMedicalEquipment $academicDegree): View|Factory|Application
    {
        return $this->editBlade(['categoryMedicalEquipment' => $academicDegree]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EquipmentDategoryRequest $request
     * @param CategoryMedicalEquipment $categoryMedicalEquipment
     *
     * @return RedirectResponse
     */
    public function update(EquipmentDategoryRequest $request, CategoryMedicalEquipment $categoryMedicalEquipment): RedirectResponse
    {
        $this->contract->update($categoryMedicalEquipment, $request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.update_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CategoryMedicalEquipment $categoryMedicalEquipment
     *
     * @return RedirectResponse
     */
    public function destroy(CategoryMedicalEquipment $categoryMedicalEquipment): RedirectResponse
    {
        try {
            $this->contract->remove($categoryMedicalEquipment);
            return $this->redirectBack()->with('success', __('messages.actions_messages.delete_success'));
        }catch (Exception $e){
            return $this->redirectBack()->with('error', $e->getMessage());
        }
    }

    /**
     * active & inactive the specified resource from storage.
     * @param CategoryMedicalEquipment $academicategoryMedicalEquipmentcDegree
     * @return RedirectResponse
     */
    public function changeActivation(CategoryMedicalEquipment $categoryMedicalEquipment): RedirectResponse
    {
        $this->contract->toggleField($categoryMedicalEquipment, 'is_active');
        return $this->redirectBack()->with('success', __('messages.actions_messages.update_success'));
    }

}
