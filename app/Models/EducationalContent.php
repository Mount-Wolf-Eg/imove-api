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

class EducationalContent extends Model
{
    use ModelTrait, SearchTrait, HasTranslations; // SoftDeletes
    public const ADDITIONAL_PERMISSIONS = [];
    protected $table = "educational_contents";
    protected $fillable = ['author_id','title', 'content', 'medical_speciality_id', 'publish_date',
        'views', 'likes', 'dislikes', 'shares', 'is_active'];
    protected array $filters = ['keyword', 'medicalSpeciality', 'isPublished', 'isMine',
        'mostLiked', 'active'];
    protected array $searchable = ['title', 'content'];
    protected array $dates = ['publish_date'];
    public array $filterModels = ['MedicalSpeciality'];
    public array $filterCustom = [];
    public array $translatable = ['title', 'content'];

    //---------------------relations-------------------------------------
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function mainImage(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable')
            ->where('type', FileConstants::FILE_TYPE_EDUCATIONAL_MAIN_IMAGE);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function medicalSpeciality(): BelongsTo
    {
        return $this->belongsTo(MedicalSpeciality::class);
    }

    public function consultations(): BelongsToMany
    {
        return $this->belongsToMany(Consultation::class, 'consultation_educational_content')
            ->withPivot('doctor_id')
            ->withTimestamps();
    }
    //---------------------relations-------------------------------------

    //---------------------Scopes-------------------------------------

    public function scopeOfMostLiked($query)
    {
        return $query->withCount('likes')->orderBy('likes_count', 'desc');
    }

    public function scopeOfMedicalSpeciality($query, $medicalSpecialityIds)
    {
        return $query->whereIn('medical_speciality_id', (array)$medicalSpecialityIds);
    }

    public function scopeOfIsPublished($query)
    {
        return $query->whereNotNull('publish_date');
    }

    public function scopeOfIsMine($query)
    {
        return $query->where('author_id', auth()->id() ?? auth('sanctum')->id());
    }

    public function scopeOfTitleStartsWith($query, $letter, $locale = 'en')
    {
        return $query->whereRaw("SUBSTRING(COALESCE(title->>'$locale', ''), 1, 1) = ?", [$letter]);
    }
    //---------------------Scopes-------------------------------------

    //---------------------Attributes-------------------------------------
    public function authLikeStatus(): Attribute
    {
        return Attribute::get(function () {
            return $this->likes->contains('user_id', auth()->id() ?? auth('sanctum')->id());
        });
    }
    //---------------------Attributes-------------------------------------


}
