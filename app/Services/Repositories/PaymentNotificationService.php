<?php

namespace App\Services\Repositories;

use App\Constants\NotificationTypeConstants;
use App\Models\Payment;
use App\Repositories\Contracts\NotificationContract;

class PaymentNotificationService
{
    private NotificationContract $notificationContract;
    private array $notifiedUsers = [];
    private array $notificationData = [];

    public function __construct(NotificationContract $notificationContract)
    {
        $this->notificationContract = $notificationContract;
        $this->notificationData = [
            'title' => 'messages.notification_messages.payment.%s.title',
            'body' => 'messages.notification_messages.payment.%s.body',
            'type' => '',
            'redirect_type' => 'Payment',
            'redirect_id' => '',
            'users' => $this->notifiedUsers,
            'data' => []
        ];
    }

    public function paymentAccepted(Payment $payment): void
    {
        if ($payment->patient?->id) {
            $this->notifiedUsers = [$payment->patient->id];
            $this->notify($payment, 'accepted');
        }
    }

    public function paymentRejected(Payment $payment): void
    {
        if ($payment->patient?->id) {
            $this->notifiedUsers = [$payment->patient->id];
            $this->notify($payment, 'rejected');
        }
    }

    public function paymentDeducted(Payment $payment): void
    {
        if ($payment->patient?->id) {
            $this->notifiedUsers = [$payment->patient->id];
            $this->notify($payment, 'deducted');
        }
    }

    private function notify(Payment $payment, string $message): void
    {
        if (empty($this->notifiedUsers)) return;

        $this->notificationData['type'] = NotificationTypeConstants::PAYMENT->value;
        $this->notificationData['title'] = __(sprintf($this->notificationData['title'], $message));
        $this->notificationData['body'] = __(sprintf($this->notificationData['body'], $message));
        $this->notificationData['redirect_id'] = $payment->id;
        $this->notificationData['users'] = $this->notifiedUsers;
        $this->notificationData['data'] = [
            'amount' => $payment->amount,
            'status' => $payment->status,
        ];

        $this->notificationContract->create($this->notificationData);
    }
}

