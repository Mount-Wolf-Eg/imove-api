<?php

namespace App\Models;

use App\Constants\FileConstants;
use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class MedicalSpeciality extends Model
{
    use SoftDeletes, ModelTrait, SearchTrait, HasTranslations;
    public const ADDITIONAL_PERMISSIONS = [];
    protected $fillable = ['name', 'description', 'percentage', 'is_active'];
    protected array $filters = ['keyword', 'active', 'doctorsCanAcceptUrgentCases'];
    protected array $searchable = ['name', 'description'];
    protected array $dates = [];
    public array $filterModels = [];
    public array $filterCustom = [];
    public array $translatable = ['name', 'description'];
    protected array $definedRelations = ['doctors', 'coupons', 'articles', 'consultations'];
    protected $with = ['icon'];

    //---------------------relations-------------------------------------

    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class, 'doctor_medical_speciality')->withPivot('price')->withTimestamps();
    }

    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_medical_speciality');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function icon()
    {
        return $this->morphOne(File::class, 'fileable')
            ->where('type', FileConstants::MEDICAL_SPECIALTY_ICON)->latest();
    }
    //---------------------relations-------------------------------------

    //---------------------Scopes-------------------------------------
    public function scopeOfDoctorsCanAcceptUrgentCases($query, $value)
    {
        return $query->whereHas('doctors', function ($query) use ($value) {
            $query->ofCanAcceptUrgentCases();
        });
    }
    //---------------------Scopes-------------------------------------

}
