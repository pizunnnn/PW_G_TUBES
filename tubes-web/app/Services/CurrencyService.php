<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.exchangerate-api.com/v4/latest/';
    
    public function __construct()
    {
        // Using free API that doesn't require key
        // Alternative: https://api.exchangerate.host/latest
    }
    
    /**
     * Get exchange rates from IDR to other currencies
     * Cache for 1 hour
     */
    public function getExchangeRates($baseCurrency = 'IDR')
    {
        try {
            $cacheKey = "exchange_rates_{$baseCurrency}";
            
            return Cache::remember($cacheKey, 3600, function () use ($baseCurrency) {
                $response = Http::timeout(10)->get($this->baseUrl . $baseCurrency);
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    Log::info('Exchange rates fetched', [
                        'base' => $baseCurrency,
                        'date' => $data['date'] ?? 'unknown'
                    ]);
                    
                    return $data['rates'] ?? [];
                }
                
                Log::warning('Exchange rate API failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return $this->getFallbackRates();
            });
            
        } catch (\Exception $e) {
            Log::error('Exchange rate fetch error', [
                'message' => $e->getMessage()
            ]);
            
            return $this->getFallbackRates();
        }
    }
    
    /**
     * Convert amount from IDR to target currency
     */
    public function convert($amount, $toCurrency = 'USD')
    {
        $rates = $this->getExchangeRates('IDR');
        
        if (isset($rates[$toCurrency])) {
            return round($amount * $rates[$toCurrency], 2);
        }
        
        return $amount;
    }
    
    /**
     * Format currency with symbol
     */
    public function formatWithSymbol($amount, $currency = 'USD')
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'IDR' => 'Rp',
        ];
        
        $symbol = $symbols[$currency] ?? $currency;
        
        if ($currency === 'IDR') {
            return $symbol . ' ' . number_format($amount, 0, ',', '.');
        }
        
        return $symbol . number_format($amount, 2);
    }
    
    /**
     * Get popular currencies for display
     */
    public function getPopularCurrencies()
    {
        return [
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            'JPY' => 'Japanese Yen',
            'SGD' => 'Singapore Dollar',
            'MYR' => 'Malaysian Ringgit',
        ];
    }
    
    /**
     * Fallback rates if API fails
     */
    protected function getFallbackRates()
    {
        return [
            'USD' => 0.000064,  // 1 IDR ≈ 0.000064 USD
            'EUR' => 0.000059,
            'GBP' => 0.000051,
            'JPY' => 0.0095,
            'SGD' => 0.000086,
            'MYR' => 0.00030,
        ];
    }
}