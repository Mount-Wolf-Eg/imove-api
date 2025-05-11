<?php

namespace App\Constants;
use App\Traits\ConstantsTrait;

enum NotificationTypeConstants : int
{
    use ConstantsTrait;

    case PATIENT = 1;
    case DOCTOR = 2;
    case VENDOR = 3;
    case PAYMENT = 4;

    public function getLabels($value):string
    {
        return match ($value) {
            self::PATIENT => __('messages.patient'),
            self::DOCTOR => __('messages.doctor'),
            self::VENDOR => __('messages.vendor'),
            self::PAYMENT => __('messages.payment'),
        };
    }

    public function label(): string
    {
        return self::getLabels($this);
    }
}
