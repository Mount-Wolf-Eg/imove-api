<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\MedicalEquipmentRequest;
use App\Models\MedicalEquipment;
use App\Repositories\Contracts\MedicalEquipmentContract;
use App\Repositories\Contracts\CategoryMedicalEquipmentContract;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseWebController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class MedicalEquipmentController extends BaseWebController
{
    private CategoryMedicalEquipmentContract $categoryMedicalEquipmentContract;

    /**
     * MedicalEquipmentController constructor.
     * @param MedicalEquipmentContract $contract
     * @param CategoryMedicalEquipmentContract $categoryMedicalEquipmentContract
     */
    public function __construct(MedicalEquipmentContract $contract, CategoryMedicalEquipmentContract $categoryMedicalEquipmentContract)
    {
        parent::__construct($contract, 'dashboard');
        $this->categoryMedicalEquipmentContract = $categoryMedicalEquipmentContract;
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
        $category = $this->categoryMedicalEquipmentContract->search([], [], ['limit' => 0, 'page' => 0]);
        return $this->indexBlade(['resources' => $resources,'category' => $category]);
    }

     /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        $category = $this->categoryMedicalEquipmentContract->search(['active' => true], [], ['limit' => 0, 'page' => 0]);
        return $this->createBlade(['category' => $category]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MedicalEquipmentRequest $request
     *
     * @return RedirectResponse
     */
    public function store(MedicalEquipmentRequest $request): RedirectResponse
    {
        $this->contract->create($request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.create_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param MedicalEquipment $medicalEquipment
     *
     * @return View|Factory|Application
     */
    public function show(MedicalEquipment $medicalEquipment): View|Factory|Application
    {
        return $this->showBlade(['medicalEquipment' => $medicalEquipment]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param MedicalEquipment $medicalEquipment
     *
     * @return View|Factory|Application
     */
    public function edit(MedicalEquipment $medicalEquipment): View|Factory|Application
    {
        $category = $this->categoryMedicalEquipmentContract->search(['active' => true], [], ['limit' => 0, 'page' => 0]);
        return $this->editBlade(['medicalEquipment' => $medicalEquipment, 'category' => $category]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MedicalEquipmentRequest $request
     * @param MedicalEquipment $medicalEquipment
     *
     * @return RedirectResponse
     */
    public function update(MedicalEquipmentRequest $request, MedicalEquipment $medicalEquipment): RedirectResponse
    {
        $this->contract->update($medicalEquipment, $request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.update_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param MedicalEquipment $medicalEquipment
     *
     * @return RedirectResponse
     */
    public function destroy(MedicalEquipment $medicalEquipment): RedirectResponse
    {
       $this->contract->remove($medicalEquipment);
       return $this->redirectBack()->with('success', __('messages.actions_messages.delete_success'));
    }

    /**
     * active & inactive the specified resource from storage.
     * @param MedicalEquipment $medicalEquipment
     * @return RedirectResponse
     */
    public function changeActivation(MedicalEquipment $medicalEquipment): RedirectResponse
    {
        $this->contract->toggleField($medicalEquipment, 'is_active');
        return $this->redirectBack()->with('success', __('messages.actions_messages.update_success'));
    }
}
