<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\DoctorRegisterRequest;
use App\Http\Requests\DoctorScheduleRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PatientRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Contracts\DoctorContract;
use App\Repositories\Contracts\UserContract;
use App\Services\Repositories\UserAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class NewAuthController extends BaseApiController
{
    private UserAuthService $userAuthService;

    private array $doctorRelations = [
        'doctor.medicalSpecialities',
        'doctor.academicDegree',
        'doctor.attachments',
        'doctor.city',
        'doctor.scheduleDays.shifts.availableSlots',
        'doctor.hospitals',
        'doctor.universities.academicDegree',
        'doctor.universities.certificate',
        'doctor.universities.university',
        'doctor.universities.medicalSpeciality'
    ];

    private array $patientRelations = ['patient.diseases'];
    private UserContract $userContract;

    public function __construct(UserContract $userContract, UserAuthService $userAuthService)
    {
        parent::__construct($userContract, UserResource::class);
        $this->userAuthService = $userAuthService;
        $this->userContract = $userContract;
    }

    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'phone' => config('validations.phone.req')
        ]);

        $existedUser = $this->contract->findBy('phone', $request->phone, false);

        if ($existedUser) {
            $existedUser = $this->userAuthService->sendVerificationCode($existedUser);
            // $hasDoctor   = $existedUser->doctor()->exists();

            return $this->respondWithSuccess('', [
                'verification_code' => $existedUser->verification_code,
                'has_patient'       => $existedUser->patient()->exists(),
                'has_doctor'        => $existedUser->doctor()->exists(),
                'doctor_is_active'  => (bool) $existedUser->doctor_is_active // $existedUser->is_active && $existedUser->doctor?->is_active && $existedUser->doctor?->request_status == 2 ? 1 : 0,
            ]);
        } else {
            $newUser = $this->contract->create(['phone' => $request->phone]);
            $newUser = $this->userAuthService->sendVerificationCode($newUser);
            return $this->respondWithSuccess('', [
                'verification_code' => $newUser->verification_code,
                'has_patient'       => false,
                'has_doctor'        => false,
                'doctor_is_active'  => false
            ]);
        }
    }

    public function login(LoginRequest $request)
    {
        $loginUser = $this->contract->findByFields(['and' => ['phone' => $request->phone, 'verification_code' => $request->verification_code]]);

        if (env('APP_ENV') == 'local' && $request->verification_code == 1234 && !$loginUser) {
            // $loginUser = $this->contract->findByFields(['and' => ['phone' => $request->phone, 'is_active' => true]]); // for testing remove this line in production
            $loginUser = \App\Models\User::where('phone', $request->phone)->first();
        }

        if ($loginUser && $loginUser->is_active) {
            if ($loginUser->doctor && ! $loginUser->doctor->is_active) {
                return $this->respondWithError(__('messages.not_active_account'), 422);
            }

            Auth::login($loginUser);

            $this->userAuthService->verifyUser($loginUser);

            $loginUser->api_token = $this->userAuthService->createToken($loginUser, $request->validated());

            $loginUser->load('patient', 'doctor');

            return $this->respondWithModel($loginUser);
        } else {
            if ($loginUser && !$loginUser->is_active) return $this->respondWithError(__('auth.not_active'), 422);
            return $this->respondWithError(__('auth.failed'), 422);
        }
    }

    public function registerUserAsPatient(PatientRegisterRequest $request)
    {
        $patient = $this->userAuthService->registerUserAsPatient($request->validated());
        $user    = $patient->user;

        $user->load('patient');
        return $this->respondWithModel($user);
    }

    public function registerUserAsDoctor(DoctorRegisterRequest $request)
    {
        $doctor = $this->userAuthService->registerUserAsDoctor($request->validated());
        $user   = $doctor->user;

        $user->load($this->doctorRelations);
        return $this->respondWithModel($user);
    }

    public function updateSchedule(DoctorScheduleRequest $request)
    {
        $doctor = auth()->user()->doctor;
        $doctor = resolve(DoctorContract::class)->update($doctor, $request->validated());
        $user   = $doctor->user->load(['doctor.scheduleDays.shifts.availableSlots'] + $this->doctorRelations);

        return $this->respondWithModel($user);
    }

    public function logout()
    {
        $user = Auth::user();

        $user->tokens()->delete();
        $this->userAuthService->setUserAsNotVerified($user);

        return $this->respondWithSuccess();
    }

    public function profile()
    {
        $relations = array_merge($this->doctorRelations, $this->patientRelations);
        return $this->respondWithModel(auth()->user()->load($relations));
    }

    public $durations = [
        '10',
        '15',
        '20',
        '30',
        '45',
        '60',
        '90',
        '120',
    ];

    public $urgentDurations = [
        '15',
        '20',
        '30',
        '45',
        '60',
        '120',
        '180',
        '360',
        '720',
        '1440',
        '2880',
    ];

    public function urgentReminderDurations()
    {
        return response()->json(['durations' => $this->urgentDurations]);
    }

    public function reminderDurations()
    {
        return response()->json(['durations' => $this->durations]);
    }

    public function updateReminderDurations(Request $request)
    {
        $request->validate([
            'reminder_before_consultation' => 'required|in:' . implode(',', $this->durations)
        ]);

        $user = auth()->user();

        if ($user) {
            $this->userContract->update($user instanceof \App\Models\User ? $user : \App\Models\User::find($user->id), ['reminder_before_consultation' => $request->reminder_before_consultation]);
        } else {
            return response()->json(['message' => __('auth.user_not_authenticated')], 401);
        }

        return response()->json(['message' => __('messages.updated_successfully')]);
    }

    public function updateUrgentReminderDurations(Request $request)
    {
        $request->validate([
            'reminder_before_consultation' => 'required|in:' . implode(',', $this->urgentDurations)
        ]);

        $user = auth()->user();

        if ($user) {
            $this->userContract->update($user instanceof \App\Models\User ? $user : \App\Models\User::find($user->id), ['urgent_reminder_before_consultation' => $request->reminder_before_consultation]);
        } else {
            return response()->json(['message' => __('auth.user_not_authenticated')], 401);
        }

        return response()->json(['message' => __('messages.updated_successfully')]);
    }
}
