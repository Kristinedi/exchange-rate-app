<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_exchange_rate_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 30);
            $table->foreignId('from_currency_id')->constrained('currencies');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_exchange_rate_tables');
    }
};
