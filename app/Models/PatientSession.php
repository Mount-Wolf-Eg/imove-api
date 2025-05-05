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
    protected $with = ['program', 'consultation'];

    //---------------------relations-------------------------------------
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class, 'consultation_id');
    }
    public function sessionExercises(): HasMany
    {
        return $this->hasMany(PatientSessionExercise::class, 'session_id');
    }
    //---------------------relations-------------------------------------

    //---------------------Scopes-------------------------------------

    //---------------------Scopes-------------------------------------

}
