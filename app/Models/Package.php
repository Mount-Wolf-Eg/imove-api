<?php

namespace App\Models;

use App\Constants\FileConstants;
use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Package extends Model
{
    use SoftDeletes, ModelTrait, SearchTrait, HasTranslations;
    public const ADDITIONAL_PERMISSIONS = [];
    protected $fillable = ['user_id', 'name', 'description', 'num_of_sessions', 'duration', 'price', 'is_active'];
    protected array $filters = ['keyword', 'active', 'owner', 'myCurrentSubscription', 'previousSubscriptions', 'doctorId', 'isValidForUser'];
    protected array $searchable = ['name', 'description'];
    protected array $dates = [];
    public array $filterModels = [];
    public array $filterCustom = [];
    public array $translatable = [];

    public function image(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable')->where('type', FileConstants::FILE_TYPE_PACKAGE_IMAGE);
    }

    //---------------------relations-------------------------------------
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function previousSubscriptions()
    {
        return $this->hasMany(Subscription::class)->where('is_paid', true)->whereDate('end_date', '<', now());
    }

    public function myCurrentSubscription()
    {
        return $this->hasOne(Subscription::class)->where('patient_id', auth()->user()->id)
            ->where('is_active', true)
            ->where('is_paid', true)
            ->whereRaw('num_of_sessions > used_num_of_sessions')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->latest();
    }
    //---------------------relations-------------------------------------

    //---------------------Scopes-------------------------------------
    public function scopeOfActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfDoctorId($query, $doctorId)
    {
        return $query->where('user_id', $doctorId);
    }

    public function scopeOfOwner($query)
    {
        return $query->where('user_id', auth()->user()->id);
    }

    public function scopeOfMyCurrentSubscription($query)
    {
        return $query->whereHas('subscriptions', function ($q) {
            $q->where('patient_id', auth()->user()->id)
                ->where('is_active', true)
                ->where('is_paid', true)
                ->whereRaw('num_of_sessions > used_num_of_sessions')
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now());
        });
    }

    public function scopeOfPreviousSubscriptions($query)
    {
        return $query->whereHas('subscriptions', function ($q) {
            $q->where('patient_id', auth()->user()->id)
                ->where('is_paid', true)
                ->whereDate('end_date', '<', now());
        });
    }

    public function scopeOfIsValidForUser($query, $userId) {
        return $query->whereHas('subscriptions', function ($q) use ($userId) {
            $q->where('patient_id', $userId)
                ->where('is_active', true)
                ->where('is_paid', true)
                ->whereRaw('num_of_sessions > used_num_of_sessions')
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now());
        });
    }
    //---------------------Scopes-------------------------------------

}
