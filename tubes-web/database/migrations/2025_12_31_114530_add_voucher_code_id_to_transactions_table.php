<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('voucher_code_id')->nullable()->after('total_price')->constrained('voucher_codes')->onDelete('set null');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('voucher_code_id'); // discount from product or voucher
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['voucher_code_id']);
            $table->dropColumn(['voucher_code_id', 'discount_amount']);
        });
    }
};
