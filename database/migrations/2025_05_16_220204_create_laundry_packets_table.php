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
        Schema::create('laundry_packets', function (Blueprint $table) {
            $table->id();

            $table->string('name', length: 100);
            $table->string('alias', length: 100);
            $table->unsignedInteger('base_price');
            $table->text('description')->nullable();;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laundry_packets');
    }
};
