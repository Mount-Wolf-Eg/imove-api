<?php

namespace App\Http\Requests;

use App\Constants\ConsultationPaymentTypeConstants;
use App\Models\Package;
use App\Repositories\Contracts\CouponContract;
use App\Rules\ValidCouponRule;
use App\Services\Repositories\PaymentCalculator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class PackageSubscribeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) auth()->user()->patient;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            // 'package_id'   => 'required|exists:packages,id',
            'coupon_code'  => ['nullable', 'exists:coupons,code', new ValidCouponRule()],
            'payment_type' => [
                'required',
                'integer',
                'in:' . implode(',', array_column(ConsultationPaymentTypeConstants::cases(), 'value')),
            ],
        ];

        return array_merge($rules, (new ConsultationRequest())->rules());
    }

    public function prepareForValidation(): void
    {
        if ((int) request(('payment_type')) === ConsultationPaymentTypeConstants::WALLET->value) {
            $amount = Package::find(request('package_id'))->price;

            if (request('coupon_code')) {
                $coupon = resolve(CouponContract::class)->findBy('code', request('coupon_code'), false);
                if ($coupon?->isValidForUser(auth()->user()->patient->id, request('medical_speciality_id'))) {
                    $amount = $coupon->applyDiscount($amount);
                }
            }

            $amount = app(PaymentCalculator::class)->calc($amount)['total_amount'];

            if ($amount > auth()->user()->wallet) {
                abort(422, __('messages.insufficient_wallet_balance'));
            }
        }
    }

    public function validated($key = null, $default = null)
    {
        $package = Package::findOrFail($this->route('package'));

        $validated = parent::validated($key, $default);

        // Manual validation using ConsultationRequest rules
        $consultationRules = (new ConsultationRequest())->rules();

        $consultationValidator = Validator::make($this->all(), $consultationRules);

        $consultationValidated = $consultationValidator->validated();

        return array_merge($validated, $consultationValidated, [
            'doctor_id'       => $package->user->doctor->id,
            'user_id'         => $package->user->id,
            'package_id'      => $package->id,
            'is_active'       => true,
            'start_date'      => now(),
            'end_date'        => now()->addDays($package->duration),
            'amount'          => $package->price,
            'num_of_sessions' => $package->num_of_sessions,
        ]);
    }
}
