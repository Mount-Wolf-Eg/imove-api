<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\SettingConsultationRequest;
use App\Repositories\Contracts\SettingConsultationContract;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseWebController;
use App\Models\SettingConsultation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SettingConsultationController extends BaseWebController
{
    /**
     * SettingConsultationController constructor.
     * @param SettingConsultationContract $contract
     */
    public function __construct(SettingConsultationContract $contract)
    {
        parent::__construct($contract, 'dashboard', null, false);
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
     * @param SettingConsultationRequest $request
     *
     * @return RedirectResponse
     */
    public function store(SettingConsultationRequest $request): RedirectResponse
    {
        $this->contract->create($request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.create_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param SettingConsultation $settingConsultation
     *
     * @return View|Factory|Application
     */
    public function show(SettingConsultation $settingConsultation): View|Factory|Application
    {
        return $this->showBlade(['settingConsultation' => $settingConsultation]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param SettingConsultation $settingConsultation
     *
     * @return View|Factory|Application
     */
    public function edit(SettingConsultation $settingConsultation): View|Factory|Application
    {
        return $this->editBlade(['settingConsultation' => $settingConsultation]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SettingConsultationRequest $request
     * @param SettingConsultation $settingConsultation
     *
     * @return RedirectResponse
     */
    public function update(SettingConsultationRequest $request, SettingConsultation $settingConsultation): RedirectResponse
    {
        $this->contract->update($settingConsultation, $request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.update_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SettingConsultation $settingConsultation
     *
     * @return RedirectResponse
     */
    public function destroy(SettingConsultation $settingConsultation): RedirectResponse
    {
        try {
            $this->contract->remove($settingConsultation);
            return $this->redirectBack()->with('success', __('messages.actions_messages.delete_success'));
        }catch (Exception $e){
            return $this->redirectBack()->with('error', $e->getMessage());
        }
    }
}
