<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\VoucherCode;
use App\Mail\VoucherCodeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class VoucherCodeService
{
    /**
     * Generate voucher codes untuk transaction setelah payment success
     * 
     * @param Transaction $transaction
     * @return array
     */
    public function generateForTransaction(Transaction $transaction)
    {
        try {
            $product = $transaction->product;
            $quantity = $transaction->quantity;
            
            $voucherCodes = [];
            
            // Generate voucher codes based on quantity
            for ($i = 0; $i < $quantity; $i++) {
                $code = VoucherCode::generateCode($product->code_format);
                
                $voucherCode = VoucherCode::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'code' => $code,
                    'is_used' => false,
                ]);
                
                $voucherCodes[] = $voucherCode;
            }
            
            Log::info('Voucher codes generated successfully', [
                'transaction_id' => $transaction->id,
                'transaction_code' => $transaction->transaction_code,
                'quantity' => $quantity,
                'codes' => collect($voucherCodes)->pluck('code')->toArray()
            ]);
            
            // Send email with voucher codes
            $this->sendVoucherEmail($transaction, $voucherCodes);
            
            return $voucherCodes;
            
        } catch (\Exception $e) {
            Log::error('Voucher code generation failed', [
                'transaction_id' => $transaction->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Send voucher codes via email to user
     * 
     * @param Transaction $transaction
     * @param array $voucherCodes
     * @return void
     */
    protected function sendVoucherEmail(Transaction $transaction, $voucherCodes)
    {
        try {
            Mail::to($transaction->user->email)
                ->send(new VoucherCodeMail($transaction, $voucherCodes));
            
            Log::info('Voucher email sent successfully', [
                'transaction_id' => $transaction->id,
                'email' => $transaction->user->email,
                'codes_count' => count($voucherCodes)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Voucher email send failed', [
                'transaction_id' => $transaction->id,
                'email' => $transaction->user->email,
                'message' => $e->getMessage()
            ]);
            
            // Don't throw exception, just log it
            // Email failure shouldn't break the payment flow
            // User can still see voucher codes in their transaction history
        }
    }
    
    /**
     * Generate manual voucher codes (untuk admin)
     * 
     * @param int $productId
     * @param int $quantity
     * @return array
     */
    public function generateManualCodes($productId, $quantity = 1)
    {
        try {
            $product = \App\Models\Product::findOrFail($productId);
            $voucherCodes = [];
            
            for ($i = 0; $i < $quantity; $i++) {
                $code = VoucherCode::generateCode($product->code_format);
                
                $voucherCode = VoucherCode::create([
                    'transaction_id' => null, // No transaction, manual generation
                    'product_id' => $product->id,
                    'code' => $code,
                    'is_used' => false,
                ]);
                
                $voucherCodes[] = $voucherCode;
            }
            
            Log::info('Manual voucher codes generated', [
                'product_id' => $productId,
                'product_name' => $product->name,
                'quantity' => $quantity
            ]);
            
            return $voucherCodes;
            
        } catch (\Exception $e) {
            Log::error('Manual voucher code generation failed', [
                'product_id' => $productId,
                'quantity' => $quantity,
                'message' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Mark voucher code as used
     * 
     * @param string $code
     * @return bool
     */
    public function markAsUsed($code)
    {
        try {
            $voucherCode = VoucherCode::where('code', $code)->firstOrFail();
            
            if ($voucherCode->is_used) {
                throw new \Exception('Voucher code already used');
            }
            
            $voucherCode->markAsUsed();
            
            Log::info('Voucher code marked as used', [
                'code' => $code,
                'voucher_code_id' => $voucherCode->id
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to mark voucher code as used', [
                'code' => $code,
                'message' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
}