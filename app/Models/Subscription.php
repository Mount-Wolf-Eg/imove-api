<?php

namespace App\Models;

use App\Constants\ConsultationPaymentTypeConstants;
use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Subscription extends Model
{
    use SoftDeletes, ModelTrait, SearchTrait, HasTranslations;
    public const ADDITIONAL_PERMISSIONS = [];
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'package_id',
        'coupon_id',
        'is_active',
        'start_date',
        'end_date',
        'num_of_sessions',
        'used_num_of_sessions',
        'amount',
        'doctor_amount',
        'app_amount',
        'tax_amount',
        'total_amount',
        'coupon_discount',
        'payment_type',
        'is_paid',
    ];

    protected $casts = [
        'is_active'            => 'boolean',
        'start_date'           => 'date',
        'end_date'             => 'date',
        'price'                => 'decimal:2',
        'num_of_sessions'      => 'integer',
        'used_num_of_sessions' => 'integer',
        'amount'               => 'decimal:2',
        'doctor_amount'        => 'decimal:2',
        'app_amount'           => 'decimal:2',
        'tax_amount'           => 'decimal:2',
        'total_amount'         => 'decimal:2',
        'coupon_discount'      => 'decimal:2',
        'payment_type'         => ConsultationPaymentTypeConstants::class,
        'is_paid'              => 'boolean',
    ];
    protected array $filters = ['keyword'];
    protected array $searchable = [];
    protected array $dates = [];
    public array $filterModels = [];
    public array $filterCustom = [];
    public array $translatable = [];

    //---------------------attributes-------------------------------------
    public function getIsActiveAttribute(): bool
    {
        return $this->start_date <= now() &&
            $this->end_date >= now() &&
            $this->is_paid &&
            $this->num_of_sessions > $this->used_num_of_sessions;
    }

    //---------------------relations-------------------------------------
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'subscription_id');
    }
    //---------------------relations-------------------------------------

    //---------------------attributes-------------------------------------

    //---------------------attributes-------------------------------------

    //---------------------Scopes-------------------------------------
    public function scopeOfActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfAvailable($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where('is_paid', true)
            ->whereRaw('num_of_sessions > used_num_of_sessions');
    }
    //---------------------Scopes-------------------------------------

}
