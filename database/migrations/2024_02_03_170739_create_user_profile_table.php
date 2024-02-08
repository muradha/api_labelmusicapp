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
        Schema::create('user_profile', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('birth_date')->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('company_name', 100)->nullable();
            $table->string('street', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable()->comment('State or Province');
            $table->string('postal_code', 10)->nullable()->comment('Zip Code or Postal Code');
            $table->string('country', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profile');
    }
};
