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
        Schema::create('laundry_transactions', function (Blueprint $table) {
            $table->string('id_transaction')->primary();
            
            $table->foreignId('id_customer')->references('id')->on('laundry_customers');
            $table->foreignId('id_payment')->references('id')->on('payments');
            $table->foreignId('id_packet')->references('id')->on('laundry_packets');

            $table->decimal('kg_amount', 8, 2)->nullable(); //
            $table->integer('total_price')->nullable();
            $table->integer('discount')->nullable();
            $table->enum('status', ['ONPROGRESS', 'PAID', 'CANCEL']);
            $table->dateTime('finish_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laundry_transactions');
    }
};
