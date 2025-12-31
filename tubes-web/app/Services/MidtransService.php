<?php

namespace App\Services;

use Midtrans\CoreApi;
use Midtrans\Config;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Create payment transaction using Core API Charge
     */
    public function createTransaction($params)
    {
        $transactionId = $params['transaction_id'];
        $grossAmount = (int) $params['gross_amount'];
        $customerDetails = $params['customer_details'];
        $itemDetails = $params['item_details'] ?? [];
        $paymentMethod = $params['payment_method'] ?? 'qris';
        $channel = $params['channel'] ?? null;

        \Log::info("[MIDTRANS] Creating {$paymentMethod} payment via Core API");

        // Prepare base transaction parameter
        $parameter = [
            'payment_type' => $this->mapPaymentType($paymentMethod),
            'transaction_details' => [
                'order_id' => $transactionId,
                'gross_amount' => $grossAmount
            ],
            'customer_details' => [
                'first_name' => $customerDetails['first_name'] ?? $customerDetails['name'] ?? 'Customer',
                'last_name' => $customerDetails['last_name'] ?? '',
                'email' => $customerDetails['email'] ?? 'customer@example.com',
                'phone' => $customerDetails['phone'] ?? ''
            ],
            'item_details' => !empty($itemDetails) ? $itemDetails : [[
                'id' => 'ITEM-1',
                'price' => $grossAmount,
                'quantity' => 1,
                'name' => 'Product Purchase'
            ]]
        ];

        // Add payment method specific parameters
        $this->addPaymentMethodParams($parameter, $paymentMethod, $channel);

        try {
            // Charge via Core API
            $chargeResponse = CoreApi::charge($parameter);

            \Log::info('[MIDTRANS] Charge response', ['response' => $chargeResponse]);

            // Extract payment instructions
            $paymentInstructions = $this->extractPaymentInstructions($chargeResponse, $paymentMethod);

            return [
                'transaction_id' => $transactionId,
                'external_id' => $chargeResponse->transaction_id ?? null,
                'payment_url' => null,
                'token' => null,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
                'channel' => $channel,
                'payment_instructions' => $paymentInstructions,
                'created_at' => now()->toISOString(),
                'expires_at' => $this->getExpiryTime(),
                'raw_response' => $chargeResponse
            ];
        } catch (\Exception $e) {
            \Log::error('[MIDTRANS] Create transaction error', ['error' => $e->getMessage()]);
            throw new \Exception("Failed to create Midtrans transaction: {$e->getMessage()}");
        }
    }

    /**
     * Map payment method to Midtrans payment_type
     */
    private function mapPaymentType($paymentMethod)
    {
        $typeMap = [
            'qris' => 'qris',
            'virtual_account' => 'bank_transfer',
            'e_wallet' => 'gopay',
            'transfer_bank' => 'bank_transfer',
            'kartu_kredit' => 'credit_card',
            'midtrans' => 'bank_transfer'
        ];

        return $typeMap[$paymentMethod] ?? 'qris';
    }

    /**
     * Add payment method specific parameters
     */
    private function addPaymentMethodParams(&$parameter, $paymentMethod, $channel)
    {
        switch ($paymentMethod) {
            case 'qris':
                $parameter['qris'] = [
                    'acquirer' => 'gopay'
                ];
                break;

            case 'virtual_account':
            case 'transfer_bank':
            case 'midtrans':
                $bank = $this->mapBankChannel($channel);
                $parameter['bank_transfer'] = [
                    'bank' => $bank
                ];
                break;

            case 'e_wallet':
                if ($channel === 'gopay' || $channel === 'GoPay') {
                    $parameter['payment_type'] = 'gopay';
                    $parameter['gopay'] = [
                        'enable_callback' => true,
                        'callback_url' => config('app.url') . '/payment/processing'
                    ];
                } elseif ($channel === 'shopeepay' || $channel === 'ShopeePay') {
                    $parameter['payment_type'] = 'shopeepay';
                    $parameter['shopeepay'] = [
                        'callback_url' => config('app.url') . '/payment/processing'
                    ];
                }
                break;

            case 'kartu_kredit':
                $parameter['payment_type'] = 'credit_card';
                $parameter['credit_card'] = [
                    'secure' => true,
                    'bank' => $channel ?? 'bni',
                    'installment_term' => 0
                ];
                break;
        }
    }

    /**
     * Map channel to Midtrans bank code
     */
    private function mapBankChannel($channel)
    {
        $bankMap = [
            'BCA' => 'bca',
            'BNI' => 'bni',
            'BRI' => 'bri',
            'Mandiri' => 'mandiri',
            'Permata' => 'permata',
            'bca' => 'bca',
            'bni' => 'bni',
            'bri' => 'bri',
            'mandiri' => 'mandiri',
            'permata' => 'permata'
        ];

        return $bankMap[$channel] ?? 'bca';
    }

    /**
     * Extract payment instructions from charge response
     */
    private function extractPaymentInstructions($response, $paymentMethod)
    {
        $instructions = [
            'type' => $paymentMethod,
            'status' => $response->transaction_status ?? 'pending',
            'transaction_id' => $response->transaction_id ?? null,
            'order_id' => $response->order_id ?? null,
            'gross_amount' => $response->gross_amount ?? 0,
            'currency' => $response->currency ?? 'IDR'
        ];

        switch ($paymentMethod) {
            case 'qris':
                if (isset($response->actions)) {
                    foreach ($response->actions as $action) {
                        if ($action->name === 'generate-qr-code') {
                            $instructions['qr_string'] = $action->url ?? '';
                        }
                    }
                }
                $instructions['acquirer'] = $response->acquirer ?? 'gopay';
                $instructions['actions'] = $response->actions ?? [];
                break;

            case 'virtual_account':
            case 'transfer_bank':
            case 'midtrans':
                if (isset($response->va_numbers) && count($response->va_numbers) > 0) {
                    $instructions['va_number'] = $response->va_numbers[0]->va_number;
                    $instructions['bank'] = $response->va_numbers[0]->bank;
                } elseif (isset($response->permata_va_number)) {
                    $instructions['va_number'] = $response->permata_va_number;
                    $instructions['bank'] = 'permata';
                } elseif (isset($response->bill_key)) {
                    $instructions['bill_key'] = $response->bill_key;
                    $instructions['biller_code'] = $response->biller_code;
                    $instructions['bank'] = 'mandiri';
                }
                break;

            case 'e_wallet':
                if (isset($response->actions) && count($response->actions) > 0) {
                    foreach ($response->actions as $action) {
                        if ($action->name === 'deeplink-redirect') {
                            $instructions['deeplink_url'] = $action->url ?? '';
                        } elseif ($action->name === 'generate-qr-code') {
                            $instructions['qr_string'] = $action->url ?? '';
                        }
                    }
                    $instructions['actions'] = $response->actions;
                }
                break;

            case 'kartu_kredit':
                $instructions['redirect_url'] = $response->redirect_url ?? '';
                break;
        }

        $instructions['expiry_time'] = $response->expiry_time ?? $this->getExpiryTime();

        return $instructions;
    }

    /**
     * Get expiry time (24 hours from now)
     */
    private function getExpiryTime()
    {
        return now()->addHours(24)->toISOString();
    }

    /**
     * Process notification from Midtrans webhook
     */
    public function processNotification($notificationData)
    {
        try {
            $statusResponse = CoreApi::status($notificationData['order_id']);

            return [
                'transaction_id' => $statusResponse->order_id,
                'external_id' => $statusResponse->transaction_id,
                'transaction_status' => $this->mapTransactionStatus($statusResponse->transaction_status),
                'payment_type' => $statusResponse->payment_type,
                'gross_amount' => $statusResponse->gross_amount,
                'transaction_time' => $statusResponse->transaction_time,
                'settlement_time' => $statusResponse->settlement_time ?? null,
                'fraud_status' => $statusResponse->fraud_status ?? null,
                'status_code' => $statusResponse->status_code,
                'raw_notification' => $statusResponse
            ];
        } catch (\Exception $e) {
            \Log::error('[MIDTRANS] Process notification error', ['error' => $e->getMessage()]);
            throw new \Exception("Failed to process notification: {$e->getMessage()}");
        }
    }

    /**
     * Map Midtrans transaction status to internal status
     */
    private function mapTransactionStatus($midtransStatus)
    {
        $statusMap = [
            'capture' => 'paid',
            'settlement' => 'paid',
            'pending' => 'pending',
            'deny' => 'failed',
            'cancel' => 'failed',
            'expire' => 'expired',
            'refund' => 'refund',
            'partial_refund' => 'refund'
        ];

        return $statusMap[$midtransStatus] ?? 'failed';
    }
}
