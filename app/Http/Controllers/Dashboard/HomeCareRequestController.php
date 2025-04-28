<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\HomeCareStatusRequest;
use App\Models\HomeCareRequest;
use App\Models\MedicalSpeciality;
use App\Repositories\Contracts\HomeCareRequestContract;
use App\Repositories\Contracts\MedicalSpecialityContract;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseWebController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class HomeCareRequestController extends BaseWebController
{
    protected MedicalSpecialityContract $medicalSpecialityContract;

    /**
     * HomeCareRequestContract constructor.
     * @param HomeCareRequestContract $contract
     * 
     * @param MedicalSpecialityContract $medicalSpecialityContract
     */
    public function __construct(HomeCareRequestContract $contract, MedicalSpecialityContract $medicalSpecialityContract)
    {
        parent::__construct($contract, 'dashboard');
        $this->medicalSpecialityContract = $medicalSpecialityContract;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        $resources = $this->contract->searchWeb($request->all(), ['patient.user', 'city', 'medicalSpeciality']);
        $MedicalSpeciality = $this->medicalSpecialityContract->search([], [], ['limit' => 0, 'page' => 0]);
        return $this->indexBlade(['resources' => $resources, 'medicalSpeciality' => $MedicalSpeciality]);
    }


    /**
     * Display the specified resource.
     *
     * @param HomeCareRequest $homeCareRequest
     *
     * @return View|Factory|Application
     */
    public function show(HomeCareRequest $homeCareRequest): View|Factory|Application
    {
        $homeCareRequest->load('patient', 'city', 'medicalSpeciality');
        return $this->showBlade(['homeCareRequest' => $homeCareRequest]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param HomeCareRequest $homeCareRequest
     *
     * @return RedirectResponse
     */
    public function destroy(HomeCareRequest $homeCareRequest): RedirectResponse
    {
       $this->contract->remove($homeCareRequest);
       return $this->redirectBack()->with('success', __('messages.actions_messages.delete_success'));
    }

       /**
     * Update the status of the specified home care request.
     *
     * @param HomeCareStatusRequest $request
     * @param HomeCareRequest $homeCareRequest
     * @return RedirectResponse
     */
    public function updateStatus(HomeCareStatusRequest $request, HomeCareRequest $homeCareRequest): RedirectResponse
    {
        $this->contract->update($homeCareRequest, ['status' => $request->status]);

        $message = $request->status == 3 ? __('messages.actions_messages.reject_success') : __('messages.actions_messages.visited_success');
        return redirect()->route('home-care-requests.show', $homeCareRequest->id)
            ->with('success', $message);
    }

}
