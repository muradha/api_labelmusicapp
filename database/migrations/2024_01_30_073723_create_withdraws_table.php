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
        Schema::create('withdraws', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->integer('amount');
            $table->enum('status', ['PROCESSING', 'APPROVED', 'REJECTED'])->default('PROCESSING');
            $table->string('address');
            $table->string('province', 100);
            $table->string('city', 100);
            $table->integer('postal_code');
            $table->morphs('withdrawable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraws');
    }
};
