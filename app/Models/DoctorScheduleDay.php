<?php

namespace App\Models;

use App\Constants\ConsultationStatusConstants;
use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class DoctorScheduleDay extends Model
{
    use SoftDeletes, ModelTrait, SearchTrait, HasTranslations;
    public const ADDITIONAL_PERMISSIONS = [];
    protected $fillable = ['doctor_id', 'date', 'has', 'is_active'];
    protected array $filters = ['keyword', 'active', 'doctor', 'date', 'afterNowDateTime', 'yearAndMonthAndHaveSlots'];
    protected array $searchable = [];
    protected array $dates = ['date'];
    public array $filterModels = [];
    public array $filterCustom = [];
    public array $translatable = [];

    //---------------------relations-------------------------------------
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function scheduleDayShifts(): HasMany
    {
        return $this->hasMany(DoctorScheduleDayShift::class);
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(DoctorScheduleDayShift::class)->whereNull('parent_id');
    }

    public function slots(): HasMany
    {
        return $this->hasMany(DoctorScheduleDayShift::class)->whereNotNull('parent_id');
    }

    public function availableSlots(): HasMany
    {
        return $this->hasMany(DoctorScheduleDayShift::class)->ofAvailableSlots();
    }

    public function nearestAvailableSlot(): HasMany
    {
        return $this->hasMany(DoctorScheduleDayShift::class)->ofAvailableSlots();
    }

    public function firstNearestAvailableSlot()
    {
        return $this->hasOne(\App\Models\DoctorScheduleDayShift::class, 'doctor_schedule_day_id')
            ->join('doctor_schedule_days as dsd', 'dsd.id', '=', 'doctor_schedule_day_shifts.doctor_schedule_day_id')
            ->whereRaw("STR_TO_DATE(CONCAT(dsd.date, ' ', doctor_schedule_day_shifts.from_time), '%Y-%m-%d %H:%i:%s') > ?", [now()])
            ->whereNotNull('parent_id')
            ->where(function ($q) {
                $q->whereDoesntHave('consultation')
                    ->orWhereHas('consultation', function ($q2) {
                        $q2->where('is_active', false)
                            ->whereNotIn('status', [
                                ConsultationStatusConstants::PATIENT_CANCELLED->value,
                                ConsultationStatusConstants::DOCTOR_CANCELLED->value
                            ]);
                    });
            })
            ->orderBy('from_time');
    }
    //---------------------relations-------------------------------------

    //---------------------Scopes-------------------------------------

    public function scopeOfDoctor($query, $value)
    {
        return $query->whereIn('doctor_id', (array)$value);
    }

    public function scopeOfDate($query, $value)
    {
        return $query->whereDate('date', $value);
    }

    public function scopeOfYearAndMonthAndHaveSlots($query, $value)
    {
        return $query->whereYear('date', $value['year'])
            ->whereMonth('date', $value['month'])
            ->whereHas('availableSlots');
    }

    public function scopeOfMine($query)
    {
        return $query->ofDoctor(auth()->user()->doctor?->id);
    }

    public function scopeOfAfterNowDateTime($query)
    {
        return $query->where(function ($q) {
            $q->whereDate('date', '>', now()->toDateString())
                ->orWhere(function ($q) {
                    $q->whereDate('date', now()->toDateString())
                        ->whereHas('availableSlots', function ($q) {
                            $q->where('from_time', '>', now()->toTimeString());
                        });
                });
        });
    }
    //---------------------Scopes-------------------------------------

    //---------------------Attributes-------------------------------------

    public function dayName(): Attribute
    {
        return Attribute::make(function ($value) {
            return $this->date->translatedFormat('l');
        });
    }
}
