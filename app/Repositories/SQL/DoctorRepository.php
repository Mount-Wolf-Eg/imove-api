<?php

namespace App\Repositories\SQL;

use App\Exceptions\CantDeleteModelException;
use App\Constants\FileConstants;
use App\Models\Consultation;
use App\Models\Doctor;
use App\Models\Package;
use App\Repositories\Contracts\DoctorContract;
use App\Repositories\Contracts\FileContract;
use App\Repositories\Contracts\UserContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use function Laravel\Prompts\select;


class DoctorRepository extends BaseRepository implements DoctorContract
{
    /**
     * DoctorRepository constructor.
     * @param Doctor $model
     */
    public function __construct(Doctor $model)
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

    public function syncRelations($model, $attributes)
    {
        if (isset($attributes['specialities'])) {
            $model->medicalSpecialities()->sync($attributes['specialities']);
        }
        if (isset($attributes['attachments'])) {
            self::syncAttachments($model, $attributes);
        }
        if (isset($attributes['schedule_days'])) {
            self::syncScheduleDays($model, $attributes);
        }
        if (isset($attributes['role'])) {
            $model->user->assignRole($attributes['role']);
        }
        if (isset($attributes['universities'])) {
            self::syncUniversities($model, $attributes);
        }
        if (isset($attributes['hospitals'])) {
            $model->hospitals()->sync($attributes['hospitals']);
        }
        return $model;
    }

    public static function syncAttachments($model, $attributes)
    {
        if (is_file($attributes['attachments'][0])) {
            $attachments = collect($attributes['attachments'])->map(function ($attachment) {
                return ['file' => $attachment, 'type' => FileConstants::FILE_TYPE_DOCTOR_ATTACHMENTS->value];
            })->toArray();
            $files = resolve(FileContract::class)->createMany($attachments);
        } else {
            $files = resolve(FileContract::class)->findIds($attributes['attachments']);
        }
        foreach ($files as $file)
            $model->attachments()->save($file);
        return $model;
    }

    public static function syncScheduleDays($model, $attributes)
    {
        foreach ($attributes['schedule_days'] as $day) {
            $day['doctor_id'] = $model->id;
            $scheduleDay = resolve(DoctorScheduleDayRepository::class)->findBy('date', $day['date'], false);
            if ($scheduleDay)
                resolve(DoctorScheduleDayRepository::class)->update($scheduleDay, $day);
            else
                resolve(DoctorScheduleDayRepository::class)->create($day);
        }
        return $model;
    }

    public static function syncUniversities($model, $attributes)
    {
        foreach ($attributes['universities'] as $university) {
            $data = collect($university)->except(['certificate'])->toArray();
            if (!empty($data['university_id'])){
                $universityData = Arr::except($data, 'university_id');
                $universityModel = $model->universities()->where('university_id', $data['university_id'])->first();
                if ($universityModel)
                {
                    $universityModel->update($universityData);
                }else{
                    $universityModel = $model->universities()->create($data);
                }
            }else{
                $universityModel = $model->universities()->updateOrCreate($data);
            }
            if (!empty($university['certificate'])) {
                $file = resolve(FileContract::class)->find($university['certificate']);
                $universityModel->certificate()->save($file);
            }
        }
        return $model;
    }

    /**
     * Remove the specified doctor from storage.
     * @param Model $model
     * @return mixed
     * @throws CantDeleteModelException
     */
    public function remove(Model $model): mixed
    {
        /** @var Doctor $doctor */
        $doctor = $model;

        // Check if the doctor can be removed (wallet, consultations, packages)
        if (!$this->canRemove($doctor)) {
            // canRemove already throws CantDeleteModelException, so this is just for clarity
            throw new CantDeleteModelException(
                __('messages.errors.cannot_delete_doctor', ['model' => __('messages.modelSingle.doctor')])
            );
        }

        // Log the activity and delete (from BaseRepository)
        return parent::remove($model);
    }

    public function canRemove(Model $model): bool
    {
        /** @var Doctor $doctor */
        $doctor = $model;

        // Check parent class conditions (existing relations)
        if (!parent::canRemove($doctor)) {
            return false;
        }

        // 1. Check wallet balance
        if ($doctor->user && $doctor->user->wallet > 0) {
            throw new CantDeleteModelException(
                __('messages.errors.cannot_delete_doctor_with_wallet_balance', [
                    'model' => __('messages.modelSingle.doctor'),
                    'balance' => $doctor->user->wallet
                ])
            );
        }

        // 2. Check for upcoming consultations
        $upcomingConsultations = Consultation::where('doctor_id', $doctor->id)
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
            ->count();
            // exists()
        if ($upcomingConsultations > 0) {
            throw new CantDeleteModelException(
                __('messages.errors.cannot_delete_doctor_with_upcoming_consultations', [
                    'model' => __('messages.modelSingle.doctor'),
                    'count' => $upcomingConsultations
                ])
            );
        }

        // 3. Check for active package consultations
        $packageConsultations = Consultation::whereHas('package', function ($query) use ($doctor) {
            $query->where('user_id', $doctor->user_id);
        })
            ->where('is_active', true)
            ->whereNotIn('status', [
                \App\Constants\ConsultationStatusConstants::PATIENT_CANCELLED->value,
                \App\Constants\ConsultationStatusConstants::DOCTOR_CANCELLED->value
            ])
            ->count();

        if ($packageConsultations > 0) {
            throw new CantDeleteModelException(
                __('messages.errors.cannot_delete_doctor_with_package_consultations', [
                    'model' => __('messages.modelSingle.doctor'),
                    'count' => $packageConsultations
                ])
            );
        }

        return true;
    }

    /**
     * Get patients associated with the doctor through consultations.
     *
     * @param Doctor $doctor
     * @param string|null $nameFilter
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    // public function getPatients(Doctor $doctor, ?string $nameFilter = null, array $filters = []): LengthAwarePaginator
    // {
    //     $query = $this->model->newQuery()
    //         ->where('id', $doctor->id)
    //         ->join('consultations', 'doctors.id', '=', 'consultations.doctor_id')
    //         ->join('patients', 'consultations.patient_id', '=', 'patients.id')
    //         ->join('users', 'patients.user_id', '=', 'users.id')
    //         ->where('consultations.is_active', true)
    //         ->whereNotIn('consultations.status', [
    //             \App\Constants\ConsultationStatusConstants::PATIENT_CANCELLED->value,
    //             \App\Constants\ConsultationStatusConstants::DOCTOR_CANCELLED->value
    //         ])
    //         ->select('patients.*')
    //         ->distinct();

    //     if ($nameFilter) {
    //         $query->where('users.name', 'like', '%' . $nameFilter . '%');
    //     }

    //     $limit = $filters['limit'] ?? 10;
    //     $page = $filters['page'] ?? 1;

    //     return $query->with(['user'])->paginate($limit, ['*'], 'page', $page);
    // }

    public function getPatients(Doctor $doctor, ?string $nameFilter = null, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->where('doctors.id', $doctor->id)
            ->join('consultations', 'doctors.id', '=', 'consultations.doctor_id')
            ->join('patients', 'consultations.patient_id', '=', 'patients.id')
            ->leftJoin('users', 'patients.user_id', '=', 'users.id')
            ->where('consultations.is_active', true)
            ->whereNotIn('consultations.status', [
                \App\Constants\ConsultationStatusConstants::PATIENT_CANCELLED->value,
                \App\Constants\ConsultationStatusConstants::DOCTOR_CANCELLED->value
            ])
            ->select('patients.*')
            ->distinct();

        if ($nameFilter) {
            $query->whereNotNull('users.name')
                ->where('users.name', 'like', '%' . $nameFilter . '%');
        }

        $limit = $filters['limit'] ?? 10;
        $page = $filters['page'] ?? 1;

        return $query->with(['user.patient.diseases'])->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * Get consultations for a specific patient with the doctor.
     *
     * @param Doctor $doctor
     * @param int $patientId
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPatientConsultations(Doctor $doctor, int $patientId, array $filters = []): LengthAwarePaginator
    {
        $query = Consultation::query()
            ->where('doctor_id', $doctor->id)
            ->where('patient_id', $patientId)
            ->where('is_active', true)
            ->whereNotIn('status', [
                \App\Constants\ConsultationStatusConstants::PATIENT_CANCELLED->value,
                \App\Constants\ConsultationStatusConstants::DOCTOR_CANCELLED->value
            ]);

        $limit = $filters['limit'] ?? 10;
        $page = $filters['page'] ?? 1;

        return $query->with(['patient.user', 'doctor.user', 'medicalSpeciality'])
                     ->paginate($limit, ['*'], 'page', $page);
    }


}
