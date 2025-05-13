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
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class University extends Model
{
    use SoftDeletes, ModelTrait, SearchTrait, HasTranslations;
    public const ADDITIONAL_PERMISSIONS = [];
    protected $table = "universities";
    protected $fillable = ['name', 'city_id', 'is_active'];
    protected array $filters = ['keyword'];
    protected array $searchable = ['name'];
    protected array $dates = [];
    public array $filterModels = [];
    public array $filterCustom = [];
    public array $translatable = ['name'];

    //---------------------relations-------------------------------------
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }
    //---------------------relations-------------------------------------

    //---------------------Scopes-------------------------------------

    //---------------------Scopes-------------------------------------

}
