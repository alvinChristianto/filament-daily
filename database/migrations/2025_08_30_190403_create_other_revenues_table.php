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
        Schema::create('other_revenues', function (Blueprint $table) {
            $table->string('id_transaction')->primary();

            $table->string('title', length: 100);
            $table->foreignId('id_payment')->references('id')->on('payments');

            $table->integer('total_price')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['LUNAS', 'DP']);
            $table->dateTime('transaction_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_revenues');
    }
};
