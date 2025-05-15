<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatientSessionExercise extends Model
{
    use ModelTrait, SearchTrait, HasTranslations; //SoftDeletes
    public const ADDITIONAL_PERMISSIONS = [];
    protected $table = "patient_session_exercises";
    protected $fillable = ['program_id', 'session_id', 'exercise_id', 'sets', 'break_between_sets',
                    'weight', 'rep', 'hold_duration', 'comments', 'ease_of_exercise', 'reason_for_overtaking',
                     'complete_sets', 'patient_total_sets', 'patient_total_reps', 'patient_exercise_repetitions'];
    protected $casts = [
        'patient_exercise_repetitions' => 'array',
    ];
    protected array $filters = ['keyword'];
    protected array $searchable = [];
    protected array $dates = [];
    public array $filterModels = [];
    public array $filterCustom = [];
    public array $translatable = [];
    public $with = ['program', 'exercise', 'session'];

    //---------------------relations-------------------------------------
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
    public function session(): BelongsTo
    {
        return $this->belongsTo(PatientSession::class, 'session_id');
    }
    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }
    //---------------------relations-------------------------------------

    //---------------------Scopes-------------------------------------

    //---------------------Scopes-------------------------------------

}
