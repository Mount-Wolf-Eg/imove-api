<?php

namespace App\Http\Controllers\Dashboard;

// use App\Http\Requests\EducationalContentRequest;
use App\Http\Requests\ExerciseRequest;
use App\Models\Exercise;
use App\Models\MedicalSpeciality;
use App\Repositories\Contracts\ExerciseContract;
use App\Repositories\Contracts\MedicalSpecialityContract;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseWebController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ExerciseController extends BaseWebController
{
    protected MedicalSpecialityContract $medicalSpecialityContract;

    /**
     * ExerciseContract constructor.
     * @param ExerciseContract $contract
     * 
     * @param MedicalSpecialityContract $medicalSpecialityContract
     */
    public function __construct(ExerciseContract $contract, MedicalSpecialityContract $medicalSpecialityContract)
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
        $resources = $this->contract->searchWeb($request->all(), ['media', 'medicalSpecialities']);
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
     * @param ExerciseRequest $request
     *
     * @return RedirectResponse
     */
    public function store(ExerciseRequest $request)
    {
        $this->contract->create($request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.create_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param Exercise $exercise
     *
     * @return View|Factory|Application
     */
    public function show(Exercise $exercise): View|Factory|Application
    {
        $exercise->load('media', 'medicalSpecialities');
        return $this->showBlade(['exercise' => $exercise]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Exercise $exercise
     *
     * @return View|Factory|Application
     */
    public function edit(Exercise $exercise): View|Factory|Application
    {
        $specialities = $this->medicalSpecialityContract->search(['active' => true], [], ['limit' => 0, 'page' => 0]);
        return $this->editBlade(['exercise' => $exercise, 'specialities' => $specialities]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ExerciseRequest $request
     * @param Exercise $exercise
     *
     * @return RedirectResponse
     */
    public function update(ExerciseRequest $request, Exercise $exercise): RedirectResponse
    {
        $this->contract->update($exercise, $request->validated());
        return $this->redirectToIndex()->with('success', __('messages.actions_messages.update_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Exercise $exercise
     *
     * @return RedirectResponse
     */
    public function destroy(Exercise $exercise): RedirectResponse
    {
       $this->contract->remove($exercise);
       return $this->redirectBack()->with('success', __('messages.actions_messages.delete_success'));
    }

    /**
     * active & inactive the specified resource from storage.
     * @param Exercise $exercise
     * @return RedirectResponse
     */
    public function changeActivation(Exercise $exercise): RedirectResponse
    {
        $this->contract->toggleField($exercise, 'is_active');
        return $this->redirectBack()->with('success', __('messages.actions_messages.update_success'));
    }

}
