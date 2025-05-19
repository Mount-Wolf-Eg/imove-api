<?php

namespace App\Repositories\SQL;

use App\Exceptions\CantDeleteModelException;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\Package;
use App\Repositories\Contracts\PatientContract;
use App\Repositories\Contracts\UserContract;
use Illuminate\Database\Eloquent\Model;

class PatientRepository extends BaseRepository implements PatientContract
{
    /**
     * PatientRepository constructor.
     * @param Patient $model
     */
    public function __construct(Patient $model)
    {
        parent::__construct($model);
    }

    public function beforeCreate($attributes)
    {
        return resolve(UserContract::class)->prepareUserForRoleUsers($attributes);
    }

    public function beforeUpdate($attributes)
    {
        return resolve(UserContract::class)->prepareUserForRoleUsers($attributes);
    }

    public function syncRelations($model, $relations): void
    {
        if (isset($relations['diseases'])) {
            $model->diseases()->sync($relations['diseases']);
        }
    }

    
    /**
     * Remove the specified patient from storage.
     * @param Model $model
     * @return mixed
     * @throws CantDeleteModelException
     */
    public function remove(Model $model): mixed
    {
        /** @var Patient $patient */
        $patient = $model;

        // Check if the patient can be removed (wallet, consultations, packages)
        $this->canRemove($patient); // canRemove throws exception if conditions are not met

        // Log the activity and delete (from BaseRepository)
        return parent::remove($model);
    }

    /**
     * Check if the patient can be deleted.
     * @param Model $model
     * @return bool
     * @throws CantDeleteModelException
     */
    public function canRemove(Model $model): bool
    {
        /** @var Patient $patient */
        $patient = $model;

        // Check parent class conditions (existing relations)
        if (!parent::canRemove($patient)) {
            return false;
        }

        // 1. Check wallet balance
        if ($patient->user && $patient->user->wallet > 0) {
            throw new CantDeleteModelException(
                __('messages.errors.cannot_delete_patient_with_wallet_balance', [
                    'model' => __('messages.modelSingle.patient'),
                    'balance' => $patient->user->wallet
                ])
            );
        }

        // 2. Check for upcoming consultations
        $hasUpcomingConsultations = Consultation::where('patient_id', $patient->id)
            ->where('is_active', true)
            ->whereNotIn('status', [
                \App\Constants\ConsultationStatusConstants::PATIENT_CANCELLED->value,
                \App\Constants\ConsultationStatusConstants::DOCTOR_CANCELLED->value
            ])
            ->where(function ($query) {
                $query->whereHas('doctorScheduleDayShift', function ($q) {
                    $q->whereHas('day', function ($dayQuery) {
                        $dayQuery->where('date', '>=', now()->format('Y-m-d'));
                    });
                });
            })
            ->exists();

        if ($hasUpcomingConsultations) {
            throw new CantDeleteModelException(
                __('messages.errors.cannot_delete_patient_with_upcoming_consultations', [
                    'model' => __('messages.modelSingle.patient'),
                    'count' => 1
                ])
            );
        }

        // 3. Check for active package consultations
        // $hasPackageConsultations = Consultation::whereHas('package', function ($query) use ($patient) {
        //     $query->where('user_id', $patient->user_id);
        // })
        //     ->where('is_active', true)
        //     ->whereNotIn('status', [
        //         \App\Constants\ConsultationStatusConstants::PATIENT_CANCELLED->value,
        //         \App\Constants\ConsultationStatusConstants::DOCTOR_CANCELLED->value
        //     ])
        //     ->exists();

        // if ($hasPackageConsultations) {
        //     throw new CantDeleteModelException(
        //         __('messages.errors.cannot_delete_patient_with_package_consultations', [
        //             'model' => __('messages.modelSingle.patient'),
        //             'count' => 1
        //         ])
        //     );
        // }

        return true;
    }


}
