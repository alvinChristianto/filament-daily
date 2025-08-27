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
        Schema::create('laundry_customers', function (Blueprint $table) {
            $table->id();

            $table->string('name', length: 100);
            $table->enum('gender', ['-', 'L', 'P'])->nullable();
            $table->enum('category', ['PERSON', 'PT', 'OTHER'])->nullable();
            $table->string('phone_number', length: 20)->nullable();
            $table->string('email', length: 100)->nullable();
            $table->string('address', length: 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laundry_customers');
    }
};
