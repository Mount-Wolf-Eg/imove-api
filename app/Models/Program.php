<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Program extends Model
{
    use ModelTrait, SearchTrait, HasTranslations; // SoftDeletes
    public const ADDITIONAL_PERMISSIONS = [];
    protected $table = "programs";
    protected $fillable = ['consultation_id', 'patient_id', 'diagnosis', 
        'num_of_sessions_per_day', 'num_of_days_of_week', 'num_of_weeks', 'break_between_exercises'
    ];
    protected array $filters = ['keyword'];
    protected array $searchable = [];
    protected array $dates = [];
    public array $filterModels = [];
    public array $filterCustom = [];
    public array $translatable = [];
    public $with = ['consultation', 'exercises'];

    //---------------------relations-------------------------------------
    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class, 'consultation_id');
    }

    public function exercises(): BelongsToMany
    {
        return $this->belongsToMany(Exercise::class, 'program_exercises')
                    ->withPivot([
                        'sets', 'break_between_sets','weight',
                        'rep', 'hold_duration','comments',
                    ]);
    }
    
    public function patientSessions()
    {
        return $this->hasMany(PatientSession::class,'program_id');
    }
    
    public function patient()
    {
        return $this->belongsTo(Patient::class,'patient_id');
    }
    //---------------------relations-------------------------------------

    //---------------------Scopes-------------------------------------

    //---------------------Scopes-------------------------------------

}
