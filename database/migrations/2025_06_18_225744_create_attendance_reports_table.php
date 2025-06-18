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
        Schema::create('attendance_reports', function (Blueprint $table) {
            $table->id();
            $table->string('worker_name', length: 100);
            $table->boolean('is_present');
            $table->string('type_absence');

            $table->dateTime('in_time')->nullable();
            $table->dateTime('out_time')->nullable();

            $table->text('notes')->nullable();
            $table->dateTime('record_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_reports');
    }
};
