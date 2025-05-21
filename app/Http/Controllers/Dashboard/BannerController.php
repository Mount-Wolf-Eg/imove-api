<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\BannerRequest;
use App\Models\Banner;
use App\Repositories\Contracts\BannerContract;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseWebController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class BannerController extends BaseWebController
{
   
    /**
     * BannerContract constructor.
     * @param BannerContract $contract
     * 
     */
    public function __construct(BannerContract $contract)
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
        $resources = $this->contract->searchWeb($request->all(), ['mainImage']);
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
     * @param BannerRequest $request
     *
     * @return RedirectResponse
     */
    public function store(BannerRequest $request)
    {
        $this->contract->create($request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.create_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param Banner $banner
     *
     * @return View|Factory|Application
     */
    public function show(Banner $banner): View|Factory|Application
    {
        $banner->load('mainImage');
        return $this->showBlade(['banner' => $banner]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Banner $banner
     *
     * @return View|Factory|Application
     */
    public function edit(Banner $banner): View|Factory|Application
    {
        return $this->editBlade(['banner' => $banner]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param BannerRequest $request
     * @param Banner $banner
     *
     * @return RedirectResponse
     */
    public function update(BannerRequest $request, Banner $banner): RedirectResponse
    {
        $this->contract->update($banner, $request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.update_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Banner $banner
     *
     * @return RedirectResponse
     */
    public function destroy(Banner $banner): RedirectResponse
    {
       $this->contract->remove($banner);
       return $this->redirectBack()->with('success', __('messages.actions_messages.delete_success'));
    }


}
