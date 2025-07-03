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
        Schema::create('expenses', function (Blueprint $table) {
            $table->string('id_expenses')->primary();

            $table->string('title', length: 100);

            $table->foreignId('id_payment')->references('id')->on('payments');

            $table->dateTime('record_date');
            $table->string('category');
            $table->unsignedInteger('amount')->nullable();
            $table->string('unit')->nullable();
            $table->unsignedInteger('price_per')->nullable();
            $table->unsignedInteger('price_total')->nullable();
            $table->string('origin_from')->nullable();
            $table->text('description')->nullable();
            $table->string('image_expenses')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
