<?php

namespace App\Models;

use App\Constants\FileConstants;
use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class HomeCareRequest extends Model
{  
    use ModelTrait, SearchTrait, HasTranslations; // SoftDeletes
    public const ADDITIONAL_PERMISSIONS = [];    
    protected $table = "home_care_requests";
    protected $fillable = [
        'status','patient_id','city_id','medical_speciality_id','address','description',
    ];
    protected array $filters = ['keyword', 'medicalSpeciality', 'city', 'status'];
    protected array $searchable = [];
    protected array $dates = [];
    public array $filterModels = ['MedicalSpeciality', 'City'];
    public array $filterCustom = [];
    public array $translatable = [];
    protected $with = ['patient', 'city', 'medicalSpeciality'];

    //---------------------relations-------------------------------------

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function medicalSpeciality(): BelongsTo
    {
        return $this->belongsTo(MedicalSpeciality::class, 'medical_speciality_id');
    }

    //---------------------relations-------------------------------------
  
    //---------------------Scopes-------------------------------------

    public function scopeOfMedicalSpeciality($query, $medicalSpecialityId)
    {
        return $query->where('medical_speciality_id', $medicalSpecialityId);
    }
    public function scopeOfCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    //---------------------Scopes-------------------------------------

}
