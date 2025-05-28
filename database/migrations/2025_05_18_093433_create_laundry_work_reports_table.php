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
        Schema::create('laundry_work_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignUuid('id_transaction')->references('id_transaction')->on('laundry_transactions');

            $table->json('transaction_detail')->nullable(); //packet, kg, worker, fee
            $table->string('worker', length: 100)->nullable();
            $table->integer('fee_pekerja')->nullable();
            $table->integer('transaction_price');
            $table->integer('working_price')->nullable();
            $table->text('report_description')->nullable();
            $table->enum('status', ['SUCCESS', 'CANCEL']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laundry_work_reports');
    }
};
