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
        Schema::create('spareparts', function (Blueprint $table) {
            $table->id();
            $table->string('name', length: 100);
            $table->unsignedInteger('price');
            $table->unsignedInteger('sell_price')->nullable();
            $table->string('unit');
            $table->unsignedInteger('initial_amount');
            $table->string('origin_from')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['NEW', 'SECOND']);
            $table->string('image')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spareparts');
    }
};
