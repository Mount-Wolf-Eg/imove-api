<?php

namespace App\Models;

use App\Constants\ConsultationStatusConstants;
use App\Constants\DoctorConsultationPeriodConstants;
use App\Constants\DoctorRequestStatusConstants;
use App\Constants\FileConstants;
use App\Constants\ReminderConstants;
use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Doctor extends Model
{
    use ModelTrait, SearchTrait, SoftDeletes, HasTranslations;
    public const ADDITIONAL_PERMISSIONS = [];
    protected $fillable = [
        'user_id',
        'academic_degree_id',
        'national_id',
        'university',
        'bio',
        'urgent_consultation_enabled',
        'with_appointment_consultation_enabled',
        'experience_years',
        'consultation_period',
        'reminder_before_consultation',
        'urgent_consultation_price',
        'with_appointment_consultation_price',
        'request_status',
        'medical_id',
        'is_active',
        'general_session_enabled',
    ];
    protected array $filters = [
        'keyword',
        'requestStatus',
        'medicalSpeciality',
        'academicDegree',
        'city',
        'topRated',
        'active',
        'canAcceptUrgentCases',
        'withUpcomingShifts',
        'generalSessionEnabled',
    ];
    protected array $searchable = ['user.name'];
    protected array $dates = [];
    public array $filterModels = ['City', 'MedicalSpeciality', 'AcademicDegree', 'University', 'Hospital', 'DoctorScheduleDay'];
    public array $filterCustom = ['consultationPeriods', 'reminders'];
    public array $translatable = [];
    public $casts = [
        'request_status' => DoctorRequestStatusConstants::class
    ];
    public $with = ['user', 'rates'];

    //---------------------relations-------------------------------------
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medicalSpecialities(): BelongsToMany
    {
        return $this->belongsToMany(MedicalSpeciality::class, 'doctor_medical_speciality')
            ->withPivot('price')->withTimestamps();
    }

    public function academicDegree(): BelongsTo
    {
        return $this->belongsTo(AcademicDegree::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable')
            ->where('type', FileConstants::FILE_TYPE_DOCTOR_ATTACHMENTS);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function rates(): MorphMany
    {
        return $this->morphMany(Rate::class, 'rateable');
    }

    public function complaints(): MorphMany
    {
        return $this->morphMany(Complaint::class, 'complaintable');
    }

    public function scheduleDays(): HasMany
    {
        return $this->hasMany(DoctorScheduleDay::class);
    }

    public function scheduleDaysShifts(): HasManyThrough
    {
        return $this->hasManyThrough(DoctorScheduleDayShift::class, DoctorScheduleDay::class);
    }

    public function universities(): HasMany
    {
        return $this->hasMany(DoctorUniversity::class);
    }

    public function hospitals(): BelongsToMany
    {
        return $this->belongsToMany(Hospital::class, 'doctor_hospital')
            ->withPivot('start_date', 'end_date')->withTimestamps();
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function getHasUpcomingShiftsAttribute()
    {
        $now   = \Carbon\Carbon::now()->format('H:i'); // Current time
        $today = \Carbon\Carbon::today()->toDateString(); // Current date

        return $this->scheduleDays()
            ->whereHas('slots', function ($query) use ($now, $today) {
                $query->where(function ($q) use ($now, $today) {
                    $q->where('doctor_schedule_days.date', '>', $today)
                        ->orWhere(function ($q) use ($now, $today) {
                            $q->where('doctor_schedule_days.date', $today)
                                ->where('doctor_schedule_day_shifts.from_time', '>=', $now);
                        });
                });
            })->exists();
    }
    //---------------------relations-------------------------------------

    //---------------------Scopes-------------------------------------
    public function scopeOfWithUpcomingShifts($query)
    {
        $cancelledStatuses = [
            ConsultationStatusConstants::PATIENT_CANCELLED->value,
            ConsultationStatusConstants::DOCTOR_CANCELLED->value,
        ];

        return $query
            ->whereHas('scheduleDays.nearestAvailableSlot', function ($q) {
                $q->whereHas('day', function ($q2) {
                    $q2->where(function ($q3) {
                        $q3->whereDate('date', '>', now()->toDateString())
                            ->orWhere(function ($q4) {
                                $q4->whereDate('date', now()->toDateString())
                                    ->whereTime('from_time', '>=', now()->format('H:i:s'));
                            });
                    });
                });
            })
            // ->with([
            //     'scheduleDays' => function ($query) {
            //         $query->whereHas('nearestAvailableSlot', function ($q) {
            //             $q->whereTime('from_time', '>=', now()->format('H:i:s'))
            //                 ->whereHas('day', function ($q2) {
            //                     $q2->whereDate('date', '>=', now()->toDateString());
            //                 });
            //         })
            //             ->orderBy('date')
            //             ->limit(1);
            //     },
            //     'scheduleDays.nearestAvailableSlot' => function ($query) {
            //         $query->whereTime('from_time', '>=', now()->format('H:i:s'))
            //             ->orderBy('from_time')
            //             ->limit(1);
            //     },
            // ])
            ->orderByRaw('(
            SELECT MIN(CONCAT(doctor_schedule_days.date, " ", doctor_schedule_day_shifts.from_time))
            FROM doctor_schedule_days
            INNER JOIN doctor_schedule_day_shifts
                ON doctor_schedule_day_shifts.doctor_schedule_day_id = doctor_schedule_days.id
            WHERE doctor_schedule_days.doctor_id = doctors.id
              AND doctor_schedule_day_shifts.parent_id IS NOT NULL
              AND (
                  NOT EXISTS (
                      SELECT 1 FROM consultations
                      WHERE consultations.doctor_schedule_day_shift_id = doctor_schedule_day_shifts.id
                  )
                  OR EXISTS (
                      SELECT 1 FROM consultations
                      WHERE consultations.doctor_schedule_day_shift_id = doctor_schedule_day_shifts.id
                        AND consultations.is_active = false
                        AND consultations.status NOT IN (?, ?)
                  )
              )
              AND (
                  doctor_schedule_days.date > CURDATE()
                  OR (doctor_schedule_days.date = CURDATE() AND doctor_schedule_day_shifts.from_time >= CURTIME())
              )
        ) ASC', $cancelledStatuses);
    }

    public function scopeOfWithUpcomingShiftsOptimized($query)
    {
        return $query
            ->join('doctor_schedule_days as dsd', 'doctors.id', '=', 'dsd.doctor_id')
            ->join('doctor_schedule_day_shifts as dsds', 'dsd.id', '=', 'dsds.doctor_schedule_day_id')
            ->leftJoin('consultations as c', function ($join) {
                $join->on('dsds.id', '=', 'c.doctor_schedule_day_shift_id')
                    ->where('c.is_active', true)
                    ->whereNotIn('c.status', [
                        ConsultationStatusConstants::PATIENT_CANCELLED->value,
                        ConsultationStatusConstants::DOCTOR_CANCELLED->value
                    ]);
            })
            ->whereNull('c.id') // لا يوجد استشارة نشطة
            ->whereNotNull('dsds.parent_id') // فقط الساعات الفرعية (slots)
            ->where(function ($q) {
                $q->whereDate('dsd.date', '>', now()->toDateString())
                    ->orWhere(function ($subQ) {
                        $subQ->whereDate('dsd.date', now()->toDateString())
                            ->whereTime('dsds.from_time', '>=', now()->format('H:i:s'));
                    });
            })
            ->select('doctors.*')
            ->addSelect(DB::raw('MIN(dsd.date) as nearest_date'))
            ->addSelect(DB::raw('MIN(dsds.from_time) as nearest_time'))
            ->groupBy('doctors.id')
            ->orderBy('nearest_date')
            ->orderBy('nearest_time')
            ->with([
                'scheduleDays' => function ($query) {
                    $query->whereDate('date', '>=', now()->toDateString())
                        ->whereHas('availableSlots', function ($q) {
                            $q->whereTime('from_time', '>=', now()->format('H:i:s'));
                        })
                        ->orderBy('date')
                        ->limit(1);
                },
                'scheduleDays.availableSlots' => function ($query) {
                    $query->whereTime('from_time', '>=', now()->format('H:i:s'))
                        ->orderBy('from_time')
                        ->limit(1);
                }
            ]);
    }

    public function scopeOfRequestStatus($query, $value)
    {
        return $query->where('request_status', $value);
    }

    public function scopeOfMedicalSpeciality($query, $value)
    {
        return $query->whereHas('medicalSpecialities', function ($q) use ($value) {
            $q->whereIn('medical_speciality_id', (array)$value);
        });
    }

    public function scopeOfDate($query, $value)
    {
        return $query->whereHas('scheduleDays', function ($q) use ($value) {
            $q->whereDate('date', $value);
        });
    }
    public function scopeOfAcademicDegree($query, $value)
    {
        return $query->where('academic_degree_id', (array)$value);
    }

    public function scopeOfCity($query, $value)
    {
        return $query->where('city_id', (array)$value);
    }
    public function scopeOfTopRated($query)
    {
        return $query->withAvg('rates', 'value')->orderBy('rates_avg_value', 'desc');
    }

    public function scopeOfCanAcceptUrgentCases($query, $myUserId = null)
    {
        return $query->where('urgent_consultation_enabled', true)
            ->when($myUserId, function ($query) use ($myUserId) {
                $query->where('user_id', '!=', $myUserId);
            })
            ->ofActive()
            ->ofRequestStatus(DoctorRequestStatusConstants::APPROVED);
    }

    public function scopeOfGeneralSessionEnabled($query, $value = true)
    {
        return $query->where('general_session_enabled', $value)
            ->ofActive()
            ->ofRequestStatus(DoctorRequestStatusConstants::APPROVED);
    }
    //---------------------Scopes-------------------------------------

    public static function consultationPeriods(): array
    {
        return DoctorConsultationPeriodConstants::valuesCollection();
    }

    public static function reminders(): array
    {
        return ReminderConstants::valuesCollection();
    }
}
