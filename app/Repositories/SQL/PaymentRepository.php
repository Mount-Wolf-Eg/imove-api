<?php

namespace App\Repositories\SQL;

use App\Constants\PaymentMethodConstants;
use App\Constants\PaymentStatusConstants;
use App\Constants\PaymentTypeConstants;
use App\Models\Bank;
use App\Models\Payment;
use App\Repositories\Contracts\PaymentContract;

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
}
