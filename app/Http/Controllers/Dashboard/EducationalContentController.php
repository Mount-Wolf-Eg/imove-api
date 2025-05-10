<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\EducationalContentRequest;
use App\Models\EducationalContent;
use App\Models\MedicalSpeciality;
use App\Repositories\Contracts\EducationalContentContract;
use App\Repositories\Contracts\MedicalSpecialityContract;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseWebController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class EducationalContentController extends BaseWebController
{
    protected MedicalSpecialityContract $medicalSpecialityContract;

    /**
     * EducationalContentContract constructor.
     * @param EducationalContentContract $contract
     * 
     * @param MedicalSpecialityContract $medicalSpecialityContract
     */
    public function __construct(EducationalContentContract $contract, MedicalSpecialityContract $medicalSpecialityContract)
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
        $resources = $this->contract->searchWeb($request->all(), ['author', 'mainImage', 'medicalSpeciality', 'likes']);
        $MedicalSpeciality = $this->medicalSpecialityContract->search([], [], ['limit' => 0, 'page' => 0]);
        return $this->indexBlade(['resources' => $resources, 'medicalSpeciality' => $MedicalSpeciality]);
    }

     /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        $specialities = $this->medicalSpecialityContract->search(['active' => true], [], ['limit' => 0, 'page' => 0]);
        return $this->createBlade(['specialities' => $specialities]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EducationalContentRequest $request
     *
     * @return RedirectResponse
     */
    public function store(EducationalContentRequest $request)
    {
        $this->contract->create($request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.create_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param EducationalContent $educationalContent
     *
     * @return View|Factory|Application
     */
    public function show(EducationalContent $educationalContent): View|Factory|Application
    {
        $educationalContent->load('likes', 'author', 'medicalSpeciality', 'mainImage');
        return $this->showBlade(['educationalContent' => $educationalContent]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param EducationalContent $educationalContent
     *
     * @return View|Factory|Application
     */
    public function edit(EducationalContent $educationalContent): View|Factory|Application
    {
        $specialities = $this->medicalSpecialityContract->search(['active' => true], [], ['limit' => 0, 'page' => 0]);
        return $this->editBlade(['educationalContent' => $educationalContent, 'specialities' => $specialities]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EducationalContentRequest $request
     * @param EducationalContent $educationalContent
     *
     * @return RedirectResponse
     */
    public function update(EducationalContentRequest $request, EducationalContent $educationalContent): RedirectResponse
    {
        $this->contract->update($educationalContent, $request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.update_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param EducationalContent $educationalContent
     *
     * @return RedirectResponse
     */
    public function destroy(EducationalContent $educationalContent): RedirectResponse
    {
       $this->contract->remove($educationalContent);
       return $this->redirectBack()->with('success', __('messages.actions_messages.delete_success'));
    }

    /**
     * active & inactive the specified resource from storage.
     * @param EducationalContent $educationalContent
     * @return RedirectResponse
     */
    public function changeActivation(EducationalContent $educationalContent): RedirectResponse
    {
        $this->contract->toggleField($educationalContent, 'is_active');
        return $this->redirectBack()->with('success', __('messages.actions_messages.update_success'));
    }

}
