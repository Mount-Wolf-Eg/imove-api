<?php

namespace App\Repositories\SQL;

use App\Constants\ConsultationPaymentTypeConstants;
use App\Constants\PaymentMethodConstants;
use App\Constants\PaymentStatusConstants;
use App\Models\Subscription;
use App\Repositories\Contracts\CouponContract;
use App\Repositories\Contracts\SubscriptionContract;
use App\Services\Repositories\PaymentCalculator;

class SubscriptionRepository extends BaseRepository implements SubscriptionContract
{
    /**
     * SubscriptionRepository constructor.
     * @param Subscription $model
     */
    public function __construct(Subscription $model)
    {
        parent::__construct($model);
    }

    public function syncRelations($model, $relations)
    {
        if ($model->amount && !$model->payment) {
            $userId     = $model->patient_id;
            $doctorId   = $model->doctor_id;
            $baseAmount = $model->amount;

            // Default values before coupon
            $paymentData = [
                'payer_id'       => $userId,
                'beneficiary_id' => $doctorId,
                'amount'         => $baseAmount,
                'transaction_id' => rand(1000000000, 9999999999),
                'currency_id'    => 1,
                'payment_method' => PaymentMethodConstants::CREDIT_CARD->value,
            ];

            $finalAmount = $baseAmount;

            if (!empty($relations['coupon_code'])) {
                $coupon = resolve(CouponContract::class)->findBy('code', $relations['coupon_code'], false);

                if ($coupon?->isValidForUser($userId, $model->medical_speciality_id)) {
                    $finalAmount                    = $coupon->applyDiscount($baseAmount);
                    $paymentData['coupon_id']       = $coupon->id;
                    $paymentData['coupon_discount'] = $baseAmount - $finalAmount;
                }
            }

            $calculated = app(PaymentCalculator::class)->calc($finalAmount);

            // Append calculated values to both paymentData and model
            $paymentData = array_merge($paymentData, $calculated);

            // $doctor_amount = $baseAmount - ($paymentData['coupon_discount'] ?? 0);

            $model->update([
                'coupon_id'       => $paymentData['coupon_id'] ?? null,
                'coupon_discount' => $paymentData['coupon_discount'] ?? 0,
                'doctor_amount'   => $calculated['doctor_amount'],
                'app_amount'      => $calculated['app_amount'],
                'tax_amount'      => $calculated['tax_amount'],
                'total_amount'    => $calculated['total_amount'],
            ]);

            // If paying by wallet
            if ((int) request()->payment_type === ConsultationPaymentTypeConstants::WALLET->value) {
                $paymentData['status'] = PaymentStatusConstants::COMPLETED->value;

                // Deduct from patient's wallet and add to doctor's wallet
                $model->patient()->decrement('wallet', $calculated['total_amount']);
                $model->doctor()->increment('wallet', $calculated['doctor_amount'],);

                $model->update(['is_paid' => true]);
            }

            // Finally, create the payment record
            $model->payment()->create($paymentData);
        }

        return $model;
    }
}
