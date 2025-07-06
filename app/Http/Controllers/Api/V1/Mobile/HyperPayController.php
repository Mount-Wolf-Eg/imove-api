<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use Exception;
use Illuminate\Http\Request;
use App\Services\HyperPayService;
use App\Http\Controllers\Controller;
use App\Traits\BaseApiResponseTrait;
use App\Constants\PaymentStatusConstants;
use App\Models\{Consultation, Subscription};
use App\Constants\ConsultationStatusConstants;
use App\Services\Repositories\ConsultationNotificationService;

class HyperPayController extends Controller
{
    use BaseApiResponseTrait;
    private ConsultationNotificationService $notificationService;
    protected $hyperPayService;

    public function __construct(HyperPayService $hyperPayService, ConsultationNotificationService $notificationService)
    {
        $this->hyperPayService = $hyperPayService;
        $this->notificationService = $notificationService;
    }

    public function createCheckout(Request $request)
    {
        $validatedData = $request->validate([
            'oid'  => 'required|integer',
            'type' => 'required|in:consultation,subscription',
            'amount' => 'required|numeric|min:0.01',
            // 'currency' => 'required|string|in:SAR,USD',
            // 'payment_type' => 'required|string|in:DB,PA',
        ]);

        $orderId = $validatedData['oid'];
        $type = $validatedData['type'];
        $model = $type === 'consultation' ? Consultation::class : Subscription::class;

        if (!$model::where('id', $orderId)->withoutGlobalScopes()->exists()) {
            return $this->respondWithErrors('Order not found.', 404, [], 'Order not found.');
        }

        $checkoutData = $this->hyperPayService->createCheckout(
            $validatedData['amount'],
            'SAR',
            'DB',
            $orderId,
            $type
        );

        if ($checkoutData['checkout_id']) {
            return response()->json([
                'checkout_id' => $checkoutData['checkout_id'],
                'integrity' => $checkoutData['integrity'],
            ], 200);
        }

        return response()->json(['error' => 'Failed to create checkout'], 500);
    }

    public function checkPaymentStatus(Request $request)
    {
        try {
            $request->validate([
                'checkout_id' => 'required|string',
                'payment_brand' => 'required|string|in:VISA,MASTER,MADA,APPLEPAY,STC_PAY,URPAY',
            ]);

            $status = $this->hyperPayService->getPaymentStatus($request->checkout_id, $request->payment_brand);

            if (isset($status['result']['code'])) {
                return $this->respondWithSuccess(['status' => $status]);
            }

            return $this->respondWithErrors('Failed to retrieve payment status.', 500, [], $status['description'] ?? 'Unknown error');
        } catch (Exception $e) {
            Log::error('HyperPay Payment Status Error: ' . $e->getMessage());
            return $this->respondWithErrors('Failed to retrieve payment status.', 500, [], $e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|string', // Checkout ID من HyperPay
                'resourcePath' => 'required|string', // Track to check payment status
                'type' => 'required|in:consultation,subscription',
                'oid' => 'required|integer', // Order ID
            ]);

            $checkoutId = $request->id;
            $type = $request->type;
            $orderId = $request->oid;

            // Checking the presence of the order
            $model = $type === 'consultation' ? Consultation::class : Subscription::class;
            if (!$model::where('id', $orderId)->withoutGlobalScopes()->exists()) {
                return $this->respondWithErrors('Order not found.', 404, [], 'Order not found.');
            }

            // Check the payment status
            $status = $this->hyperPayService->getPaymentStatus($checkoutId);

            // Determining the payment status based on response code
            $isSuccess = $this->isPaymentSuccessful($status);

            if ($type === 'consultation') {
                $order = Consultation::withoutGlobalScope('isActive')->findOrFail($orderId);
                if ($isSuccess) {
                    $order->update(['is_active' => true]);
                    $order->payment()->update([
                        'transaction_id' => $status['id'] ?? $checkoutId,
                        'status' => PaymentStatusConstants::COMPLETED->value,
                    ]);
                    $order->doctor?->user()?->increment('wallet', $order->amount);
                    $order->status === ConsultationStatusConstants::URGENT_PATIENT_APPROVE_DOCTOR_OFFER->value
                        ? $this->notificationService->patientAcceptDoctorOffer($order)
                        : $this->notificationService->newConsultation($order);
                } else {
                    $order->payment()->update([
                        'transaction_id' => $status['id'] ?? $checkoutId,
                        'status' => PaymentStatusConstants::CANCELLED->value,
                    ]);
                }
            } elseif ($type === 'subscription') {
                $order = Subscription::findOrFail($orderId);
                if ($isSuccess) {
                    $order->update(['is_active' => true, 'is_paid' => true]);
                    $order->payment()->update([
                        'status' => PaymentStatusConstants::COMPLETED->value,
                        'transaction_id' => $status['id'] ?? $checkoutId,
                    ]);
                    $order->consultations()->update(['is_active' => true]);
                } else {
                    $order->update(['is_active' => false, 'is_paid' => false]);
                    $order->payment()->update([
                        'status' => PaymentStatusConstants::CANCELLED->value,
                        'transaction_id' => $status['id'] ?? $checkoutId,
                    ]);
                }
            }

            return $this->respondWithSuccess(['message' => 'Payment processed successfully']);
        } catch (Exception $e) {
            Log::error('HyperPay Callback Error: ' . $e->getMessage());
            return $this->respondWithErrors('Failed to process payment callback.', 500, [], $e->getMessage());
        }
    }


    public function webhook(Request $request)
    {
        try {
            // التحقق من صحة الطلب
            $input = $request->all();
            if (empty($input['id']) || empty($input['resourcePath'])) {
                Log::error('HyperPay Webhook Error: Invalid webhook data');
                return response()->json(['IsSuccess' => false, 'Message' => 'Invalid webhook data'], 404);
            }

            $checkoutId = $input['id'];
            $type = $request->query('type', 'consultation');
            $orderId = $request->query('oid');

            // التحقق من وجود الطلب
            $model = $type === 'consultation' ? Consultation::class : Subscription::class;
            if (!$model::where('id', $orderId)->withoutGlobalScopes()->exists()) {
                Log::error('HyperPay Webhook Error: Order not found');
                return response()->json(['IsSuccess' => false, 'Message' => 'Order not found'], 404);
            }

            // التحقق من حالة الدفع
            $status = $this->hyperPayService->getPaymentStatus($checkoutId);
            $isSuccess = $this->isPaymentSuccessful($status);

            if ($type === 'consultation') {
                $order = Consultation::withoutGlobalScope('isActive')->findOrFail($orderId);
                if ($isSuccess) {
                    $order->update(['is_active' => true]);
                    $order->payment()->update([
                        'transaction_id' => $status['id'] ?? $checkoutId,
                        'status' => PaymentStatusConstants::COMPLETED->value,
                    ]);
                    $order->doctor?->user()?->increment('wallet', $order->amount);
                    $order->status === ConsultationStatusConstants::URGENT_PATIENT_APPROVE_DOCTOR_OFFER->value
                        ? $this->notificationService->patientAcceptDoctorOffer($order)
                        : $this->notificationService->newConsultation($order);
                } else {
                    $order->payment()->update([
                        'transaction_id' => $status['id'] ?? $checkoutId,
                        'status' => PaymentStatusConstants::CANCELLED->value,
                    ]);
                }
            } elseif ($type === 'subscription') {
                $order = Subscription::findOrFail($orderId);
                if ($isSuccess) {
                    $order->update(['is_active' => true, 'is_paid' => true]);
                    $order->payment()->update([
                        'status' => PaymentStatusConstants::COMPLETED->value,
                        'transaction_id' => $status['id'] ?? $checkoutId,
                    ]);
                    $order->consultations()->update(['is_active' => true]);
                } else {
                    $order->update(['is_active' => false, 'is_paid' => false]);
                    $order->payment()->update([
                        'status' => PaymentStatusConstants::CANCELLED->value,
                        'transaction_id' => $status['id'] ?? $checkoutId,
                    ]);
                }
            }

            return response()->json(['IsSuccess' => true, 'Message' => 'Webhook processed successfully']);
        } catch (Exception $e) {
            Log::error('HyperPay Webhook Error: ' . $e->getMessage());
            return response()->json(['IsSuccess' => false, 'Message' => $e->getMessage()], 500);
        }
    }

    /**
     * Check if payment is successful based on HyperPay response.
     */
    private function isPaymentSuccessful($status)
    {
        // HyperPay response codes: https://hyperpay.docs.oppwa.com/reference/resultCodes
        $successCodes = [
            '000.000.000', // Transaction succeeded
            '000.100.110', // Request successfully processed in 'Merchant in Integrator Test Mode'
            '000.100.111', // Request successfully processed in 'Merchant in Validator Test Mode'
            '000.100.112', // Request successfully processed in 'Merchant in Simulator Test Mode'
        ];

        return isset($status['result']['code']) && in_array($status['result']['code'], $successCodes);
    }

}
