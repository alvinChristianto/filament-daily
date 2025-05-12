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
        Schema::create('ac_customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', length: 100);
            $table->enum('gender', ['-', 'L', 'P']);
            $table->enum('category', ['PERSON', 'PT', 'OTHER']);
            $table->string('phone_number', length: 20);
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
        Schema::dropIfExists('ac_customers');
    }
};
