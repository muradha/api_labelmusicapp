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
        Schema::create('analytic_store', function (Blueprint $table) {
            $table->id();
            $table->integer('download')->unsigned()->default(0);
            $table->integer('revenue')->unsigned()->default(0);
            $table->integer('streaming')->unsigned()->default(0);
            $table->foreignId('analytic_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('music_store_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytic_store');
    }
};
