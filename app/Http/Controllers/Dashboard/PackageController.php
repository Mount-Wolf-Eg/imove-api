<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\DashboardPackageRequest;
use App\Models\Package;
use App\Repositories\Contracts\PackageContract;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseWebController;
use App\Repositories\Contracts\UserContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PackageController extends BaseWebController
{
    /**
     * PackageController constructor.
     * @param PackageContract $contract
     */
    public function __construct(PackageContract $contract)
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
        $resources = $this->contract->search($request->all(), ['user', 'image']);
        return $this->indexBlade(['resources' => $resources]);
    }

     /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        return $this->createBlade(['doctors' => resolve(UserContract::class)->search([], ['doctor'], ['limit' => 0, 'page' => 0])]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DashboardPackageRequest $request
     *
     * @return RedirectResponse
     */
    public function store(DashboardPackageRequest $request): RedirectResponse
    {
        $this->contract->create($request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.create_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param Package $package
     *
     * @return View|Factory|Application
     */
    public function show(Package $package): View|Factory|Application
    {
        return $this->showBlade(['package' => $package]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Package $package
     *
     * @return View|Factory|Application
     */
    public function edit(Package $package): View|Factory|Application
    {
        return $this->editBlade(['package' => $package, 'doctors' => resolve(UserContract::class)->search([], ['doctor'], ['limit' => 0, 'page' => 0])]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param DashboardPackageRequest $request
     * @param Package $package
     *
     * @return RedirectResponse
     */
    public function update(DashboardPackageRequest $request, Package $package): RedirectResponse
    {
        $this->contract->update($package, $request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.update_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Package $package
     *
     * @return RedirectResponse
     */
    public function destroy(Package $package): RedirectResponse
    {
        try {
            $this->contract->remove($package);
            return $this->redirectBack()->with('success', __('messages.actions_messages.delete_success'));
        }catch (Exception $e){
            return $this->redirectBack()->with('error', $e->getMessage());
        }
    }

    /**
     * active & inactive the specified resource from storage.
     * @param Package $package
     * @return RedirectResponse
     */
    public function changeActivation(Package $package): RedirectResponse
    {
        $this->contract->toggleField($package, 'is_active');
        return $this->redirectBack()->with('success', __('messages.actions_messages.update_success'));
    }
}
