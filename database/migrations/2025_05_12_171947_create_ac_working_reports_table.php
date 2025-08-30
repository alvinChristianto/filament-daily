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
        Schema::create('ac_working_reports', function (Blueprint $table) {
            $table->string('id_report')->primary();

            $table->foreignId('id_customer')->references('id')->on('ac_customers');
            $table->foreignId('id_payment')->references('id')->on('payments');
            $table->foreignId('id_worker')->references('id')->on('ac_workers');

            $table->string('title', length: 100);
            $table->string('address', length: 100)->nullable();
            $table->text('working_description')->nullable();

            $table->dateTime('in_time');
            $table->dateTime('out_time');
            $table->string('image_working')->nullable();
            $table->json('transaction_detail')->nullable();     //tipe, amount

            $table->integer('total_price')->nullable();
            $table->integer('discount')->nullable();

            $table->string('nota_ac_image')->nullable();
            $table->enum('status', ['SUCCESS', 'DP', 'FAILED', 'OTHER']);
            $table->dateTime('next_service_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ac_working_reports');
    }
};
