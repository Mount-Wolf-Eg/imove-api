<?php

namespace App\Services\Repositories;

use App\Models\GeneralSettings;

class PaymentCalculator
{
    public function calc(float $amount): array
    {
        $appPercentage = GeneralSettings::getSettingValue('app_payment_percentage') / 100;
        $taxPercentage = GeneralSettings::getSettingValue('tax_percentage') / 100;

        $appAmount   = $amount * $appPercentage;
        $taxAmount   = ($amount + $appAmount) * $taxPercentage;
        $totalAmount = $amount + $appAmount + $taxAmount;

        return [
            'app_amount'   => round($appAmount, 2),
            'tax_amount'   => round($taxAmount, 2),
            'total_amount' => round($totalAmount, 2),
        ];
    }
}
