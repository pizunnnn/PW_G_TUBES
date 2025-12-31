<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voucher_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Voucher code
            $table->string('discount_type'); // 'percentage' or 'fixed'
            $table->decimal('discount_value', 10, 2); // amount or percentage
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('cascade'); // for specific game/category
            $table->decimal('min_purchase', 10, 2)->nullable(); // minimum purchase to use voucher
            $table->decimal('max_discount', 10, 2)->nullable(); // max discount for percentage type
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_until')->nullable();
            $table->integer('usage_limit')->default(1); // how many times can be used total
            $table->integer('used_count')->default(0); // how many times has been used
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index untuk performa query
            $table->index('code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_codes');
    }
};