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
        // Add JSON field to products to define what fields are needed
        Schema::table('products', function (Blueprint $table) {
            $table->json('account_fields')->nullable()->after('description');
        });

        // Add fields to transactions to store game account info
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('game_user_id')->nullable()->after('bank');
            $table->string('game_server')->nullable()->after('game_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('account_fields');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['game_user_id', 'game_server']);
        });
    }
};
