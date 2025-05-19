<?php

namespace App\Http\Controllers\Dashboard;

use App\Constants\ConsultationVendorStatusConstants;
use App\Http\Controllers\BaseWebController;
use App\Models\Consultation;
use App\Repositories\Contracts\ConsultationContract;
use App\Repositories\Contracts\DoctorContract;
use App\Services\Repositories\ConsultationVendorService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ConsultationQuestionsController extends BaseWebController
{

    private ConsultationVendorService $consultationVendorService;

    /**
     * PatientConsultationController constructor.
     * @param ConsultationContract $contract
     * @param ConsultationVendorService $consultationVendorService
     */
    public function __construct(ConsultationContract $contract, ConsultationVendorService $consultationVendorService)
    {
        parent::__construct($contract, 'dashboard');
        $this->consultationVendorService = $consultationVendorService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        $filters = $request->all();
        if (!auth()->user()->can('view-all-consultation'))
            $filters['mineAsVendor'] = true;
        $doctors = resolve(DoctorContract::class)->search([], ['user'], ['limit' => 0]);
        $vendorStatuses = collect(ConsultationVendorStatusConstants::valuesCollection());
        $types = collect(Consultation::types());
        $resources = $this->contract->search($filters, ['doctor', 'patient', 'medicalSpeciality', 'vendors', 'consultationQuestions']);
        // return $this->indexBlade(['resources' => $resources, 'doctors' => $doctors,
        //     'vendorStatuses' => $vendorStatuses, 'types' => $types]);
        return view('dashboard.consultation-questions.index', ['resources' => $resources, 'doctors' => $doctors,
            'vendorStatuses' => $vendorStatuses, 'types' => $types]);
    }


    public function show(Consultation $consultation_question): View|Factory|Application
    {
        $consultation_question->load('doctor', 'patient', 'consultationQuestions');  
        return view('dashboard.consultation-questions.show', ['consultation' => $consultation_question]);
    }


}
