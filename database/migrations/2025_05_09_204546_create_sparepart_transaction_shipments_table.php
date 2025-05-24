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
        Schema::create('sparepart_transaction_shipments', function (Blueprint $table) {
            $table->string('id_transaction')->primary();

            $table->foreignId('id_warehouse')->references('id')->on('warehouses');
            $table->foreignId('id_payment')->references('id')->on('payments');

            $table->json('transaction_detail')->nullable();

            $table->integer('total_price')->nullable();
            $table->integer('discount')->nullable();
            $table->text('description')->nullable();

            $table->string('payment_image')->nullable();
            $table->enum('status', ['SENT', 'RETURNED', 'INITIAL', 'ADD']);
            $table->dateTime('transaction_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sparepart_transaction_shipments');
    }
};
