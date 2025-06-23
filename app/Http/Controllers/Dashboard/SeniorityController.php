<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\SeniorityRequest;
use App\Models\Seniority;
use App\Repositories\Contracts\SeniorityContract;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseWebController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SeniorityController extends BaseWebController
{
    /**
     * SeniorityController constructor.
     * @param SeniorityContract $contract
     */
    public function __construct(SeniorityContract $contract)
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
     * @param SeniorityRequest $request
     *
     * @return RedirectResponse
     */
    public function store(SeniorityRequest $request): RedirectResponse
    {
        $this->contract->create($request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.create_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param Seniority $seniority
     *
     * @return View|Factory|Application
     */
    public function show(Seniority $seniority): View|Factory|Application
    {
        return $this->showBlade(['seniority' => $seniority]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Seniority $seniority
     *
     * @return View|Factory|Application
     */
    public function edit(Seniority $seniority): View|Factory|Application
    {
        return $this->editBlade(['seniority' => $seniority]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SeniorityRequest $request
     * @param Seniority $seniority
     *
     * @return RedirectResponse
     */
    public function update(SeniorityRequest $request, Seniority $seniority): RedirectResponse
    {
        $this->contract->update($seniority, $request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.update_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Seniority $seniority
     *
     * @return RedirectResponse
     */
    public function destroy(Seniority $seniority): RedirectResponse
    {
        try {
            $this->contract->remove($seniority);
            return $this->redirectBack()->with('success', __('messages.actions_messages.delete_success'));
        }catch (Exception $e){
            return $this->redirectBack()->with('error', $e->getMessage());
        }
    }

    /**
     * active & inactive the specified resource from storage.
     * @param Seniority $seniority
     * @return RedirectResponse
     */
    public function changeActivation(Seniority $seniority): RedirectResponse
    {
        $this->contract->toggleField($seniority, 'is_active');
        return $this->redirectBack()->with('success', __('messages.actions_messages.update_success'));
    }
}
