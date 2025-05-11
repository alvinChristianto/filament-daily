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
        Schema::create('sparepart_shipments', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('id_warehouse')->references('id')->on('warehouses'); 
            $table->foreignId('id_sparepart')->references('id')->on('spareparts'); 

            $table->enum('status', ['SENT', 'RETURNED', 'INITIAL', 'ADD']);
            $table->integer('remaining_stock');
            $table->integer('sent_stock');
            $table->text('description')->nullable();
            $table->dateTime('shipment_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sparepart_shipments');
    }
};
