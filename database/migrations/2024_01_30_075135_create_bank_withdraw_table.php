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
        Schema::create('bank_withdraw', function (Blueprint $table) {
            $table->id();
            $table->string('account_number', 100);
            $table->string('bank_type', 100);
            $table->string('swift_code', 100)->nullable()->comment('SWIFT/BIC Code');
            $table->string('ach_code', 100)->nullable();
            $table->string('ifsc_code', 100)->nullable()->comment('Indian Financial System Code (IFSC)');
            $table->string('currency', 50);
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_withdraw');
    }
};
