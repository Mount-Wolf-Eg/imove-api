<?php

namespace App\Services;

use Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class HyperPayService
{
    protected $client;
    protected $entityId;
    protected $authToken;
    protected $checkoutUrl;
    protected $paymentStatusUrl;
    protected $mode;

    public function __construct()
    {
        $this->client = new Client();
        $this->entityId = config('hyperpay.entity_id');
        $this->authToken = config('hyperpay.auth_token');
        $this->checkoutUrl = config('hyperpay.checkout_url');
        $this->paymentStatusUrl = config('hyperpay.payment_status_url');
        $this->mode = config('hyperpay.mode');
    }

    public function createCheckout($amount, $currency = 'SAR', $paymentType = 'DB', $orderId = null, $type = null)
    {
        try {
            $callbackUrl = route('payment.callback') . "?type={$type}&oid={$orderId}";

            $response = $this->client->post($this->checkoutUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->authToken,
                ],
                'form_params' => [
                    'entityId' => $this->entityId,
                    'amount' => number_format($amount, 2, '.', ''),
                    'currency' => $currency,
                    'paymentType' => $paymentType,
                    'merchantTransactionId' => 'txn_' . uniqid(),
                    'customer.email' => auth()->user()->email ?? 'test@vt.com.sa',
                    'billing.street1' => '123 Street',
                    'billing.city' => 'Riyadh',
                    'billing.state' => 'Riyadh',
                    'billing.country' => 'SA',
                    'billing.postcode' => '12345',
                    'testMode' => $this->mode === 'test' ? 'EXTERNAL' : '0',
                    'integrity' => 'true',
                    'notificationUrl' => $callbackUrl, // إضافة Callback URL
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return [
                'checkout_id' => $data['id'] ?? null,
                'integrity' => $data['integrity'] ?? null,
            ];
        } catch (RequestException $e) {
            Log::error('HyperPay Checkout Error: ' . $e->getMessage());
            return ['checkout_id' => null, 'integrity' => null];
        }
    }


    // public function createCheckout($amount, $currency = 'SAR', $paymentType = 'DB')
    // {
    //     try {
    //         $response = $this->client->post($this->checkoutUrl, [
    //             'headers' => [
    //                 'Authorization' => 'Bearer ' . $this->authToken,
    //             ],
    //             'form_params' => [
    //                 'entityId' => $this->entityId,
    //                 'amount' => number_format($amount, 2, '.', ''),
    //                 'currency' => $currency,
    //                 'paymentType' => $paymentType,
    //                 'merchantTransactionId' => 'txn_' . uniqid(),
    //                 'customer.email' => auth()->user()->email ?? 'test@vt.com.sa',
    //                 'billing.street1' => '123 Street',
    //                 'billing.city' => 'Riyadh',
    //                 'billing.state' => 'Riyadh',
    //                 'billing.country' => 'SA',
    //                 'billing.postcode' => '12345',
    //                 'testMode' => $this->mode === 'test' ? 'EXTERNAL' : '0',
    //                 'integrity' => 'true',
    //             ],
    //         ]);

    //         $data = json_decode($response->getBody(), true);
    //         return [
    //             'checkout_id' => $data['id'] ?? null,
    //             'integrity' => $data['integrity'] ?? null,
    //         ];
    //     } catch (RequestException $e) {
    //         Log::error('HyperPay Checkout Error: ' . $e->getMessage());
    //         return ['checkout_id' => null, 'integrity' => null];
    //     }
    // }

    public function getPaymentStatus($checkoutId, $paymentBrand = 'MADA')
    {
        try {
            $response = $this->client->get("{$this->paymentStatusUrl}/{$checkoutId}/payment", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->authToken,
                ],
                'query' => [
                    'entityId' => $this->entityId,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            Log::error('HyperPay Payment Status Error: ' . $e->getMessage());
            return ['status' => 'fail', 'description' => $e->getMessage()];
        }
    }

}
