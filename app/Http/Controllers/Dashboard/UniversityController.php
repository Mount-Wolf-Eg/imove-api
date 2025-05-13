<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\UniversityRequest;
use App\Models\University;
use App\Repositories\Contracts\UniversityContract;
use App\Repositories\Contracts\CityContract;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseWebController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class UniversityController extends BaseWebController
{
    private CityContract $cityContract;

    /**
     * UniversityController constructor.
     * @param UniversityContract $contract
     * @param CityContract $cityContract
     */
    public function __construct(UniversityContract $contract, CityContract $cityContract)
    {
        parent::__construct($contract, 'dashboard');
        $this->cityContract = $cityContract;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        $resources = $this->contract->searchWeb($request->all(), ['doctors']);
        $category = $this->cityContract->search([], [], ['limit' => 0, 'page' => 0]);
        return $this->indexBlade(['resources' => $resources,'city' => $category]);
    }

     /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        $category = $this->cityContract->search(['active' => true], [], ['limit' => 0, 'page' => 0]);
        return $this->createBlade(['city' => $category]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UniversityRequest $request
     *
     * @return RedirectResponse
     */
    public function store(UniversityRequest $request): RedirectResponse
    {
        $this->contract->create($request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.create_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param University $university
     *
     * @return View|Factory|Application
     */
    public function show(University $university): View|Factory|Application
    {
        return $this->showBlade(['university' => $university]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param University $university
     *
     * @return View|Factory|Application
     */
    public function edit(University $university): View|Factory|Application
    {
        $category = $this->cityContract->search(['active' => true], [], ['limit' => 0, 'page' => 0]);
        return $this->editBlade(['university' => $university, 'city' => $category]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UniversityRequest $request
     * @param University $university
     *
     * @return RedirectResponse
     */
    public function update(UniversityRequest $request, University $university): RedirectResponse
    {
        $this->contract->update($university, $request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.update_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param University $university
     *
     * @return RedirectResponse
     */
    public function destroy(University $university): RedirectResponse
    {
       $this->contract->remove($university);
       return $this->redirectBack()->with('success', __('messages.actions_messages.delete_success'));
    }

    /**
     * active & inactive the specified resource from storage.
     * @param University $university
     * @return RedirectResponse
     */
    public function changeActivation(University $university): RedirectResponse
    {
        $this->contract->toggleField($university, 'is_active');
        return $this->redirectBack()->with('success', __('messages.actions_messages.update_success'));
    }

}
