<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ConsultationQuestion extends Model
{
    use SoftDeletes, ModelTrait, SearchTrait, HasTranslations;
    
    public const ADDITIONAL_PERMISSIONS = [];
    protected $fillable = ['question', 'is_active'];
    protected array $filters = ['keyword', 'question'];
    protected array $searchable = ['question'];
    protected array $dates = [];
    public array $filterModels = [];
    public array $filterCustom = [];
    public array $translatable = ['question'];

    //---------------------relations-------------------------------------

    //---------------------relations-------------------------------------

    //---------------------Scopes-------------------------------------

    //---------------------Scopes-------------------------------------

}
