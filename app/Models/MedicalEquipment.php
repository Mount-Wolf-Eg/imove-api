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

class MedicalEquipment extends Model
{
    use ModelTrait, SearchTrait, HasTranslations; // SoftDeletes
    public const ADDITIONAL_PERMISSIONS = [];
    protected $table = "medical_equipment";
    protected $fillable = ['category_id', 'name', 'link', 'is_active'];
    protected array $filters = ['keyword', 'category', 'active'];
    protected array $searchable = ['name'];
    protected array $dates = [];
    public array $filterModels = ['CategoryMedicalEquipment'];
    public array $filterCustom = [];
    public array $translatable = ['name'];
    protected $with = ['photo', 'category'];

    //---------------------relations-------------------------------------

    public function photo(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable')
            ->where('type', FileConstants::MEDICAL_EQUIPMENT_PHOTO)->latest();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryMedicalEquipment::class, 'category_id');
    }

    public function consultations(): BelongsToMany
    {
        return $this->belongsToMany(Consultation::class, 'consultation_medical_equipment')
                    ->withPivot('doctor_id')
                    ->withTimestamps();
    }

    //---------------------relations-------------------------------------

    //---------------------Scopes-------------------------------------
    public function scopeOfCategory($query, $value)
    {
        return $query->where('category_id', $value);
    }
    //---------------------Scopes-------------------------------------

}
