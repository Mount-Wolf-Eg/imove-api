<?php

namespace App\Models;

use App\Constants\PaymentMethodConstants;
use App\Constants\PaymentStatusConstants;
use App\Constants\PaymentTypeConstants;
use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Payment extends Model
{
    use SoftDeletes, ModelTrait, SearchTrait, HasTranslations;
    public const ADDITIONAL_PERMISSIONS = [];
    protected $fillable = [
        'payer_id',
        'beneficiary_id',
        'payable_id',
        'payable_type',
        'coupon_id',
        'transaction_id',
        'amount',
        'currency_id',
        'payment_method',
        'status',
        'metadata',
        'bank_id',
        'type'
    ];
    protected array $filters = [
        'keyword',
        'status',
        'paymentMethod',
        'creationDate',
        'payer',
        'beneficiary',
        'fromCreationDate',
        'toCreationDate',
        'consultationType',
        'coupon'
    ];
    protected array $searchable = ['transaction_id', 'currency.name', 'payer.name', 'beneficiary.name'];
    protected array $dates = [];
    public array $filterModels = [];
    public array $filterCustom = [];
    public array $translatable = [];
    public $casts = [
        'metadata' => 'array',
        'status' => PaymentStatusConstants::class,
        'payment_method' => PaymentMethodConstants::class,
        'type' => PaymentTypeConstants::class,
    ];

    //---------------------relations-------------------------------------
    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(User::class, 'beneficiary_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    // public function bank(): BelongsTo
    // {
    //     return $this->belongsTo(Bank::class);
    // }
    //---------------------relations-------------------------------------

    //---------------------Scopes-------------------------------------
    public function scopeOfPayer($query, $payer_id)
    {
        return $query->where('payer_id', $payer_id);
    }

    public function scopeOfBeneficiary($query, $beneficiary_id)
    {
        return $query->where('beneficiary_id', $beneficiary_id);
    }

    public function scopeCreationDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOfPaymentMethod($query, $payment_method)
    {
        return $query->where('payment_method', $payment_method);
    }

    public function scopeOfFromCreationDate($query, $date)
    {
        return $query->whereDate('created_at', '>=', $date);
    }

    public function scopeOfToCreationDate($query, $date)
    {
        return $query->whereDate('created_at', '<=', $date);
    }

    public function scopeOfConsultationType($query, $type)
    {
        return $query->whereHasMorph('payable', [Consultation::class], function ($query) use ($type) {
            $query->where('type', $type);
        });
    }

    public function scopeOfCoupon($query, $coupon_id)
    {
        return $query->where('coupon_id', $coupon_id);
    }

    public function scopeOfPatient($query, $userId)
    {
        return $query->where(function ($query) use ($userId) {
            $query->where('payer_id', $userId)
                ->orWhere(function ($query) use ($userId) {
                    $query->where('beneficiary_id', $userId)
                        ->where('type', PaymentTypeConstants::REFUND->value);
                });
        });
    }
    //---------------------Scopes-------------------------------------

    //---------------------Attributes-------------------------------------
    public function appPercentage(): Attribute
    {
        $appPaymentPercentage = GeneralSettings::getSettingValue('app_payment_percentage');
        return Attribute::make(fn() => $this->amount * $appPaymentPercentage);
    }

    public function doctorPercentage(): Attribute
    {
        return Attribute::make(fn() => $this->amount - $this->app_percentage);
    }
    //---------------------Attributes-------------------------------------
}
