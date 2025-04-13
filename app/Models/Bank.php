<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Bank extends Model
{
    use ModelTrait, SearchTrait, HasTranslations; // SoftDeletes

    public const ADDITIONAL_PERMISSIONS = [];
    protected $fillable = ['user_id', 'name', 'account_number', 'iban'];
    protected array $filters = ['keyword', 'auth'];
    protected array $searchable = [];
    protected array $dates = [];
    public array $filterModels = [];
    public array $filterCustom = [];
    public array $translatable = [];

    //---------------------relations-------------------------------------
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }
    //---------------------relations-------------------------------------

    //---------------------Scopes-------------------------------------
    public function scopeOfAuth($query)
    {
        return $query->where('user_id', auth()->id());
    }
    //---------------------Scopes-------------------------------------

}
