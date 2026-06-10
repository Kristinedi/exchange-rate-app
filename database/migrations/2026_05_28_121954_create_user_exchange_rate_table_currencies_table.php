<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_exchange_rate_table_currencies', function (Blueprint $table) {
            $table->foreignId('user_exchange_rate_table_id')->constrained()->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->primary(['user_exchange_rate_table_id', 'currency_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_exchange_rate_table_currencies');
    }
};
