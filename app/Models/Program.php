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

class Program extends Model
{
    use ModelTrait, SearchTrait, HasTranslations; // SoftDeletes
    public const ADDITIONAL_PERMISSIONS = [];
    protected $table = "programs";
    protected $fillable = ['consultation_id', 'diagnosis', 'num_of_sessions_per_day', 'num_of_days_of_week', 'num_of_weeks', 'break_between_exercises'];
    protected array $filters = ['keyword'];
    protected array $searchable = [];
    protected array $dates = [];
    public array $filterModels = [];
    public array $filterCustom = [];
    public array $translatable = [];
    public $with = [];

    //---------------------relations-------------------------------------
    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class, 'consultation_id');
    }
    //---------------------relations-------------------------------------

    //---------------------Scopes-------------------------------------

    //---------------------Scopes-------------------------------------

}
