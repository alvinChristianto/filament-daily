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
        Schema::create('ac_working_complains', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['ONPROGRESS', 'DONE']);

            $table->foreignUuid('id_report')->references('id_report')->on('ac_working_reports');

            $table->string('description_complain')->nullable();
            $table->string('description_solving')->nullable();
            $table->string('image_complain')->nullable();
            $table->string('image_solving')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ac_working_complains');
    }
};
