<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\StaticPageRequest;
use App\Models\StaticPage;
use App\Repositories\Contracts\StaticPageContract;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseWebController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class StaticPageController extends BaseWebController
{
    /**
     * StaticPageController constructor.
     * @param StaticPageContract $contract
     */
    public function __construct(StaticPageContract $contract)
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
     * @param StaticPageRequest $request
     *
     * @return RedirectResponse
     */
    public function store(StaticPageRequest $request): RedirectResponse
    {
        $this->contract->create($request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.create_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param StaticPage $StaticPage
     *
     * @return View|Factory|Application
     */
    public function show(StaticPage $StaticPage): View|Factory|Application
    {
        return $this->showBlade(['staticPage' => $StaticPage]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param StaticPage $StaticPage
     *
     * @return View|Factory|Application
     */
    public function edit(StaticPage $StaticPage): View|Factory|Application
    {
        return $this->editBlade(['staticPage' => $StaticPage]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StaticPageRequest $request
     * @param StaticPage $StaticPage
     *
     * @return RedirectResponse
     */
    public function update(StaticPageRequest $request, StaticPage $StaticPage): RedirectResponse
    {
        $this->contract->update($StaticPage, $request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.update_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param StaticPage $StaticPage
     *
     * @return RedirectResponse
     */
    public function destroy(StaticPage $StaticPage): RedirectResponse
    {
        try {
            $this->contract->remove($StaticPage);
            return $this->redirectBack()->with('success', __('messages.actions_messages.delete_success'));
        }catch (Exception $e){
            return $this->redirectBack()->with('error', $e->getMessage());
        }
    }



}
