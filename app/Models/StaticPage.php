<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class StaticPage extends Model
{
    use ModelTrait, SearchTrait, HasTranslations; // SoftDeletes
    public const ADDITIONAL_PERMISSIONS = [];
    protected $table = "static_pages";
    protected $fillable = ['content', 'page'];
    protected array $filters = ['keyword'];
    protected array $searchable = ['content'];
    protected array $dates = [];
    public array $filterModels = [];
    public array $filterCustom = [];
    public array $translatable = ['content'];
    
    //---------------------relations-------------------------------------

    //---------------------relations-------------------------------------

    //---------------------Scopes-------------------------------------

    //---------------------Scopes-------------------------------------

}
