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
        Schema::create('daily_revenue_expenses', function (Blueprint $table) {
            $table->id();

            $table->dateTime('date_record');
            $table->string('title')->nullable();
            $table->string('id_transaction')->nullable();
            $table->unsignedInteger('revenue_laundry')->nullable();
            $table->unsignedInteger('revenue_serviceac')->nullable();
            $table->unsignedInteger('revenue_sparepart')->nullable();

            $table->unsignedInteger('expense_buy_sparepart')->nullable();
            $table->unsignedInteger('expense_other')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_revenue_expenses');
    }
};
