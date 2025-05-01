<?php

namespace App\Models;

use App\Constants\FileConstants;
use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Exercise extends Model
{
    use ModelTrait, SearchTrait, HasTranslations; // SoftDeletes
    public const ADDITIONAL_PERMISSIONS = [];
    protected $table = "exercises";
    protected $fillable = ['name', 'brief', 'description', 'is_active'];
    protected array $filters = ['keyword'];
    protected array $searchable = ['name', 'brief', 'description'];
    protected array $dates = [];
    public array $filterModels = [];
    public array $filterCustom = [];
    public array $translatable = ['name', 'brief', 'description'];
    public $with = ['media', 'medicalSpecialities'];

    //---------------------relations-------------------------------------

    public function media(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable')
            ->where('type', FileConstants::FILE_TYPE_EXERCISE_MEDIA);
    }

    public function medicalSpecialities(): BelongsToMany
    {
        return $this->belongsToMany(MedicalSpeciality::class, 'exercise_medical_specialities');
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'program_exercises');
    }
    //---------------------relations-------------------------------------

    //---------------------Scopes-------------------------------------

    //---------------------Scopes-------------------------------------

}
