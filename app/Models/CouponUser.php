<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class CouponUser extends Model
{
    use SoftDeletes, ModelTrait, SearchTrait, HasTranslations;
    public const ADDITIONAL_PERMISSIONS = [];
    protected $table = "coupon_users";
    protected $fillable = [];
    protected array $filters = ['keyword'];
    protected array $searchable = [];
    protected array $dates = [];
    public array $filterModels = [];
    public array $filterCustom = [];
    public array $translatable = [];

    //---------------------relations-------------------------------------

    //---------------------relations-------------------------------------

    //---------------------Scopes-------------------------------------

    //---------------------Scopes-------------------------------------

}
