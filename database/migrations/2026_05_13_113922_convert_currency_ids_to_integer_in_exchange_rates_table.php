<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->unsignedBigInteger('currency_id')->change();
            $table->unsignedBigInteger('base_currency_id')->change();
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->foreign('base_currency_id')->references('id')->on('currencies');
        });
    }

    public function down(): void
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropForeign(['base_currency_id']);
            $table->string('currency_id')->change();
            $table->string('base_currency_id')->change();
        });
    }
};
