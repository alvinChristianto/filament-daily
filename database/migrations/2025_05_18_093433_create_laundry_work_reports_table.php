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
            $table->string('id_report')->primary();

            $table->foreignUuid('id_transaction')->references('id_transaction')->on('laundry_transactions');

            $table->string('cuci_worker')->nullable();
            $table->decimal('cuci_kg_amount', 8, 2)->nullable();
            $table->integer('cuci_fee')->nullable();

            $table->string('lipat_worker')->nullable();
            $table->decimal('lipat_kg_amount', 8, 2)->nullable();
            $table->integer('lipat_fee')->nullable();

            $table->string('setrika_worker')->nullable();
            $table->decimal('setrika_kg_amount', 8, 2)->nullable();
            $table->integer('setrika_fee')->nullable();

            $table->integer('transaction_price');
            $table->integer('working_price')->nullable();
            $table->text('report_description')->nullable();
            $table->enum('status', ['ONGOING', 'SUCCESS', 'CANCEL']);
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
