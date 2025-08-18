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
        Schema::create('sparepart_stocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_warehouse')->references('id')->on('warehouses');
            $table->foreignId('id_sparepart')->nullable()->references('id')->on('spareparts')->onDelete('set null');;

            $table->string('id_transaction')->nullable();
            $table->enum('status', ['STOCK_IN', 'STOCK_SOLD_MAINSTORE', 'STOCK_SOLD_AC', 'RETURNED']);
            $table->integer('amount')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('stock_record_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sparepart_stocks');
    }
};
