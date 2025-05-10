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


class PatientSession extends Model
{
    use ModelTrait, SearchTrait, HasTranslations; //SoftDeletes
    public const ADDITIONAL_PERMISSIONS = [];
    protected $table = "patient_sessions";
    protected $fillable = ['program_id', 'consultation_id', 'week', 'day',
                    'degree_of_pain', 'extent_of_improvement', 'comments', 'end_date'];
    protected array $filters = ['keyword'];
    protected array $searchable = [];
    protected array $dates = [];
    public array $filterModels = [];
    public array $filterCustom = [];
    public array $translatable = [];
    protected $casts = [
        'end_date' => 'datetime',
    ];
    protected $with = ['program', 'consultation', 'sessionExercises'];

    //---------------------relations-------------------------------------
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class, 'consultation_id');
    }


    public function sessionExercises(): BelongsToMany
    {
        return $this->belongsToMany(Exercise::class, 'patient_session_exercises', 'session_id', 'exercise_id')
                    ->withPivot([
                        'id', 'program_id', 'sets', 'break_between_sets', 'weight',
                        'rep', 'hold_duration', 'comments', 'ease_of_exercise', 'reason_for_overtaking',
                     'complete_sets', 'patient_total_sets', 'patient_total_reps', 'patient_exercise_repetitions',
                     'updated_at', 'created_at',
                    ]);
    }
    

    //---------------------relations-------------------------------------

    //---------------------Scopes-------------------------------------

    //---------------------Scopes-------------------------------------

}
