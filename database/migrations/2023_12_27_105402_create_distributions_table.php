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
        Schema::create('distributions', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200)->unique();
            $table->string('language_title', 200)->nullable();
            $table->enum('release_type', ['SINGLE', 'ALBUM'])->default('SINGLE');
            $table->date('release_date');
            $table->date('release_date_original')->nullable();
            $table->tinyInteger('explicit_content', false, false)->default(0);
            $table->string('UPC')->nullable();
            $table->string('cover')->nullable();
            $table->string('country', 100)->nullable();
            $table->string('copyright', 250)->nullable();
            $table->year('copyright_year')->nullable();
            $table->string('publisher', 100)->nullable()->default('text');
            $table->year('publisher_year')->nullable();
            $table->string('label', 250)->nullable();
            $table->tinyInteger('submit_status', false, false)->default(0);
            $table->enum('verification_status', ['PENDING','REJECTED','APPROVED'])->default('PENDING');
            $table->text('description')->nullable();
            $table->foreignId('artist_id')->constrained()->onUpdate('cascade')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};
