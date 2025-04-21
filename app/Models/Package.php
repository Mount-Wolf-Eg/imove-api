<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Package extends Model
{
    use SoftDeletes, ModelTrait, SearchTrait, HasTranslations;
    public const ADDITIONAL_PERMISSIONS = [];
    protected $fillable = ['user_id', 'name', 'description', 'num_of_sessions', 'price', 'is_active'];
    protected array $filters = ['keyword', 'active', 'owner'];
    protected array $searchable = ['name', 'description'];
    protected array $dates = [];
    public array $filterModels = [];
    public array $filterCustom = [];
    public array $translatable = [];

    //---------------------relations-------------------------------------
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    //---------------------relations-------------------------------------

    //---------------------Scopes-------------------------------------
    public function scopeOfActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfOwner($query)
    {
        return $query->where('user_id', auth()->user()->id);
    }
    //---------------------Scopes-------------------------------------

}
