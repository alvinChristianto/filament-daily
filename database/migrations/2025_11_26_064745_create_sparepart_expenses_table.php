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
        Schema::create('sparepart_expenses', function (Blueprint $table) {
            $table->id();

            $table->string('supplier_name', length: 100);
            
            $table->json('expense_sparepart_detail')->nullable(); //sparepart_name, buy_price, status, buy_amount etc

            $table->integer('expense_price_total')->nullable();
            $table->text('expense_notes')->nullable();
            $table->json('expense_payment_detail')->nullable();
            $table->foreignId('id_payment')->references('id')->on('payments');
            $table->enum('status', ['LUNAS', 'DP']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sparepart_expenses');
    }
};
