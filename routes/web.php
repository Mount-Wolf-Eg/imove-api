<?php

use App\Http\Controllers\Dashboard\ContactController;
use App\Http\Controllers\Dashboard\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\AboutController;
use App\Http\Controllers\Dashboard\FaqController;
use App\Http\Controllers\Front\ContactController as ContactUsController;
use App\Http\Controllers\Front\DoctorsController;
use App\Http\Controllers\Dashboard\FileController;
use App\Http\Controllers\Dashboard\NoteController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\CouponController;
use App\Http\Controllers\Dashboard\DoctorController;
use App\Http\Controllers\Dashboard\VendorController;
use App\Http\Controllers\Dashboard\ArticleController;
use App\Http\Controllers\Dashboard\DiseaseController;
use App\Http\Controllers\Dashboard\PatientController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\OverviewController;
use App\Http\Controllers\Dashboard\FaqSubjectController;
use App\Http\Controllers\Dashboard\ConsultationController;
use App\Http\Controllers\Dashboard\VendorServiceController;
use App\Http\Controllers\Dashboard\AcademicDegreeController;
use App\Http\Controllers\Dashboard\CategoryMedicalEquipmentController;
use App\Http\Controllers\Dashboard\MedicalEquipmentController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\Dashboard\MedicalSpecialityController;
use App\Http\Controllers\Auth\Passwords\ResetPasswordController;
use App\Http\Controllers\Auth\Passwords\ForgetPasswordController;
use App\Http\Controllers\Dashboard\FeaturedListController;
use App\Http\Controllers\Dashboard\GeneralSettingsController;
use App\Http\Controllers\Dashboard\ReferralController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function () {
    Route::get('/', [HomeController::class, 'index'])->name('front.home');
    Route::get('about-us', [AboutController::class, 'index'])->name('front.about');
    Route::get('doctors', [DoctorsController::class, 'index'])->name('front.doctors');
    Route::get('doctor-details', [DoctorsController::class, 'view'])->name('front.doctorDetails');
    Route::get('contact-us', [ContactUsController::class, 'index'])->name('front.contact');
    Route::middleware(['guest'])->group(function () {
        // Login
        Route::get('login', [AuthController::class, 'login'])->name('login');
        Route::post('check-credentials', [AuthController::class, 'checkCredentials'])->name('checkCredentials');
        // Reset Password
        Route::prefix('password')->group(function () {
            Route::get('request', [ForgetPasswordController::class, 'requestPassword'])->name('password.request');
            Route::post('email', [ForgetPasswordController::class, 'sendEmailPassword'])->name('password.email');
            Route::get('reset-sent-successfully', [ForgetPasswordController::class, 'emailSentSuccessfully'])->name('resetEmailSentSuccessfully');
            Route::get('reset', [ResetPasswordController::class, 'resetPassword'])->name('password.reset');
            Route::post('update', [ResetPasswordController::class, 'updatePassword'])->name('password.update');
        });
    });

    Route::middleware(['auth'])->prefix('dashboard')->group(function () {
        Route::resource('files', FileController::class)->only(['store', 'destroy']);
        Route::resource('notes', NoteController::class)->only(['store', 'update', 'destroy']);
        Route::get('/', OverviewController::class)->name('dashboard');
        Route::get('overview', [OverviewController::class, 'overview'])->name('overview');
        Route::resource('roles', RoleController::class);
        Route::put('roles/{role}/change-activation', [RoleController::class, 'changeActivation'])->name('roles.active');
        Route::resource('users', UserController::class);
        Route::put('users/{user}/change-activation', [UserController::class, 'changeActivation'])->name('users.active');
        Route::resource('academic-degrees', AcademicDegreeController::class);
        Route::put('academic-degrees/{academicDegree}/change-activation', [AcademicDegreeController::class, 'changeActivation'])->name('academic-degrees.active');
        Route::resource('medical-specialities', MedicalSpecialityController::class);
        Route::put('medical-specialities/{medicalSpeciality}/change-activation', [MedicalSpecialityController::class, 'changeActivation'])->name('medical-specialities.active');
        Route::resource('vendor-services', VendorServiceController::class);
        Route::put('vendor-services/{vendorService}/change-activation', [VendorServiceController::class, 'changeActivation'])->name('vendor-services.active');
        Route::resource('diseases', DiseaseController::class);
        Route::put('diseases/{disease}/change-activation', [DiseaseController::class, 'changeActivation'])->name('diseases.active');
        Route::resource('patients', PatientController::class);
        Route::put('patients/{patient}/change-activation', [PatientController::class, 'changeActivation'])->name('patients.active');
        Route::resource('articles', ArticleController::class);
        Route::put('articles/{article}/change-activation', [ArticleController::class, 'changeActivation'])->name('articles.active');
        Route::put('articles/{id}/publish', [ArticleController::class, 'publish'])->name('articles.publish');
        Route::resource('faq-subjects', FaqSubjectController::class);
        Route::put('faq-subjects/{faqSubject}/change-activation', [FaqSubjectController::class, 'changeActivation'])->name('faq-subjects.active');
        Route::resource('faqs', FaqController::class);
        Route::put('faqs/{faq}/change-activation', [FaqController::class, 'changeActivation'])->name('faqs.active');
        Route::resource('doctors', DoctorController::class);
        Route::put('doctors/{doctor}/change-activation', [DoctorController::class, 'changeActivation'])->name('doctors.active');
        Route::put('doctors/{doctor}/approve', [DoctorController::class, 'approve'])->name('doctors.approve');
        Route::put('doctors/{doctor}/reject', [DoctorController::class, 'reject'])->name('doctors.reject');
        Route::resource('vendors', VendorController::class);
        Route::put('vendors/{vendor}/change-activation', [VendorController::class, 'changeActivation'])->name('vendors.active');
        Route::resource('coupons', CouponController::class);
        Route::put('coupons/{coupon}/change-activation', [CouponController::class, 'changeActivation'])->name('coupons.active');
        Route::resource('consultations', ConsultationController::class)->only(['index', 'show', 'destroy']);
        Route::resource('referrals', ReferralController::class)->only(['index', 'show', 'destroy']);
        Route::put('consultations/{consultation}/vendor-accept', [ConsultationController::class, 'vendorAccept'])->name('consultations.vendor-accept');
        Route::put('consultations/{consultation}/vendor-reject', [ConsultationController::class, 'vendorReject'])->name('consultations.vendor-reject');
        Route::put('referrals/{consultation}/vendor-accept', [ReferralController::class, 'vendorAccept'])->name('referrals.vendor-accept');
        Route::put('referrals/{consultation}/vendor-reject', [ReferralController::class, 'vendorReject'])->name('referrals.vendor-reject');
        Route::resource('payments', PaymentController::class)->only(['index', 'destroy']);
        Route::get('featured-list', [FeaturedListController::class, 'edit'])->name('featured-list.edit');
        Route::put('featured-list', [FeaturedListController::class, 'update'])->name('featured-list.update');
        Route::resource('contact', ContactController::class)->only(['index']);
        Route::resource('category-medical-equipments', CategoryMedicalEquipmentController::class);
        Route::put('category-medical-equipments/{categoryMedicalEquipment}/change-activation', [CategoryMedicalEquipmentController::class, 'changeActivation'])->name('category-medical-equipments.active');
        Route::resource('medical-equipments', MedicalEquipmentController::class);
        Route::put('medical-equipments/{medicalEquipment}/change-activation', [MedicalEquipmentController::class, 'changeActivation'])->name('medical-equipments.active');


        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'profile'])->name('profile');
            Route::post('update', [ProfileController::class, 'updateProfile'])->name('profile.update');
            Route::get('change-password', [ProfileController::class, 'changePassword'])->name('change-password');
            Route::post('update-password', [ProfileController::class, 'updatePassword'])->name('update-password');
        });
        Route::get('download', [OverviewController::class, 'download'])->name('download');
        Route::get('logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/settings', [GeneralSettingsController::class, 'edit'])->name('settings.edit');
        Route::post('/settings', [GeneralSettingsController::class, 'update'])->name('settings.update');
    });
});
