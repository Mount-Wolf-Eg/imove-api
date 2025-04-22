<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\TechnicalSupport;
use App\Repositories\Contracts\TechnicalSupportContract;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseWebController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class TechnicalSupportController extends BaseWebController
{
    /**
     * TechnicalSupportController constructor.
     * @param TechnicalSupportContract $contract
     */
    public function __construct(TechnicalSupportContract $contract)
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
     * Display the specified resource.
     *
     * @param TechnicalSupport $TechnicalSupport
     *
     * @return View|Factory|Application
     */
    public function show(TechnicalSupport $TechnicalSupport): View|Factory|Application
    {
        return $this->showBlade(['technicalSupport' => $TechnicalSupport]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param TechnicalSupport $TechnicalSupport
     *
     * @return RedirectResponse
     */
    public function destroy(TechnicalSupport $TechnicalSupport): RedirectResponse
    {
        try {
            $this->contract->remove($TechnicalSupport);
            return $this->redirectBack()->with('success', __('messages.actions_messages.delete_success'));
        }catch (Exception $e){
            return $this->redirectBack()->with('error', $e->getMessage());
        }
    }



}
