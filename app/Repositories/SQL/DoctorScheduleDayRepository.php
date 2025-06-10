<?php

namespace App\Repositories\SQL;

use App\Models\DoctorScheduleDay;
use App\Repositories\Contracts\DoctorContract;
use App\Repositories\Contracts\DoctorScheduleDayContract;
use App\Repositories\Contracts\DoctorScheduleDayShiftContract;

class DoctorScheduleDayRepository extends BaseRepository implements DoctorScheduleDayContract
{
    /**
     * DoctorScheduleDayRepository constructor.
     * @param DoctorScheduleDay $model
     */
    public function __construct(DoctorScheduleDay $model)
    {
        parent::__construct($model);
    }

    public function syncRelations($model, $attributes)
    {
        if (isset($attributes['schedule_days'])) {
            foreach ($attributes['schedule_days'] as $day) {
                $this->create($day);
            }
        }

        if (isset($attributes['shifts'])) {
            $model->scheduleDayShifts()->delete();
            foreach ($attributes['shifts'] as $shift) {
                $shift['doctor_schedule_day_id'] = $model->id;
                resolve(DoctorScheduleDayShiftContract::class)->create($shift);
            }
        }

        return $model;
    }
}
