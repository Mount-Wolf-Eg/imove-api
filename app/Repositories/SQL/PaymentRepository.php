<?php

namespace App\Repositories\SQL;

use App\Constants\PaymentMethodConstants;
use App\Constants\PaymentStatusConstants;
use App\Constants\PaymentTypeConstants;
use App\Models\Bank;
use App\Models\Payment;
use App\Repositories\Contracts\PaymentContract;
use App\Services\Repositories\PaymentNotificationService;
use Illuminate\Support\Facades\DB;

class PaymentRepository extends BaseRepository implements PaymentContract
{
    /**
     * PaymentRepository constructor.
     * @param Payment $model
     */
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    public function refundRequest($user, $bank_id)
    {
        return $this->model->create([
            // 'payer_id'       => $bank_id,
            'beneficiary_id' => $user->id,
            'amount'         => $user->wallet,
            'currency_id'    => 1,
            'payable_type'   => Bank::class,
            'payable_id'     => $bank_id,
            'transaction_id' => rand(1000000000, 9999999999),
            'payment_method' => PaymentMethodConstants::BANK_TRANSFER->value,
            'status'         => PaymentStatusConstants::PENDING->value,
            'type'           => PaymentTypeConstants::REFUND->value,
        ]);
    }

    public function accept(Payment $payment): Payment
    {
        if ($payment->status->value != PaymentStatusConstants::PENDING->value) {
            throw new \DomainException('Only pending payments can be accepted.');
        }

        try {
            DB::beginTransaction();

            // Update the payment status
            $payment->update([
                'status' => PaymentStatusConstants::COMPLETED->value,
            ]);

            // Notify the patient about the acceptance
            app(PaymentNotificationService::class)->paymentAccepted($payment);

            DB::commit();

            return $payment;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \RuntimeException('Failed to accept payment: ' . $e->getMessage());
        }
    }

    public function reject(Payment $payment): Payment
    {
        if ($payment->status->value != PaymentStatusConstants::PENDING->value) {
            throw new \DomainException('Only pending payments can be rejected.');
        }

        try {
            DB::beginTransaction();

            // Update the payment status to rejected
            $payment->update([
                'status' => PaymentStatusConstants::REJECTED->value,
            ]);

            // Refund the amount to the user's wallet
            $payment->payer->update([
                'wallet' => $payment->payer->wallet + $payment->amount,
            ]);

            // Notify the patient about the rejection
            app(PaymentNotificationService::class)->paymentRejected($payment);

            DB::commit();

            return $payment;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \RuntimeException('Failed to reject payment: ' . $e->getMessage());
        }
    }
}
