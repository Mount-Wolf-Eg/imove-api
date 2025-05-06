<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\SettingPackageRequest;
use App\Repositories\Contracts\SettingPackageContract;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseWebController;
use App\Models\SettingPackage;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SettingPackageController extends BaseWebController
{
    /**
     * SettingPackageController constructor.
     * @param SettingPackageContract $contract
     */
    public function __construct(SettingPackageContract $contract)
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
        $settingPackage = $this->contract->search([], [], ['limit' => 0, 'page' => 0]);
        return $this->createBlade(['settingPackage' => $settingPackage]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SettingPackageRequest $request
     *
     * @return RedirectResponse
     */
    public function store(SettingPackageRequest $request): RedirectResponse
    {
        $this->contract->create($request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.create_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param SettingPackage $settingPackage
     *
     * @return View|Factory|Application
     */
    public function show(SettingPackage $settingPackage): View|Factory|Application
    {
        return $this->showBlade(['settingPackage' => $settingPackage]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param SettingPackage $settingPackage
     *
     * @return View|Factory|Application
     */
    public function edit(SettingPackage $settingPackage): View|Factory|Application
    {
        $settingPackage = $this->contract->search([], [], ['limit' => 0, 'page' => 0]);
        return $this->editBlade(['settingPackage' => $settingPackage]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SettingPackageRequest $request
     * @param SettingPackage $settingPackage
     *
     * @return RedirectResponse
     */
    public function update(SettingPackageRequest $request, SettingPackage $settingPackage): RedirectResponse
    {
        $this->contract->update($settingPackage, $request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.update_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SettingPackage $settingPackage
     *
     * @return RedirectResponse
     */
    public function destroy(SettingPackage $settingPackage): RedirectResponse
    {
        try {
            $this->contract->remove($settingPackage);
            return $this->redirectBack()->with('success', __('messages.actions_messages.delete_success'));
        }catch (Exception $e){
            return $this->redirectBack()->with('error', $e->getMessage());
        }
    }
}
