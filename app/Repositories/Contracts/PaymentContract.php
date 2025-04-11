<?php

namespace App\Repositories\Contracts;

interface PaymentContract extends BaseContract
{
    public function refundRequest($user, $bank_id);
}

