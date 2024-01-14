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
        Schema::create('track_music_store', function (Blueprint $table) {
            $table->id();
            $table->foreignId('track_id')->constrained()->onUpdate('cascade')->onUpdate('cascade');
            $table->foreignId('store_id')->constrained('music_stores')->onUpdate('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('track_music_store');
    }
};
