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
            $table->string('title', 200);
            $table->string('artist_name', 200);
            $table->string('version', 200)->nullable();
            $table->string('genre', 200);
            $table->string('lyric_language', 200);
            $table->enum('release_type', ['SINGLE', 'ALBUM'])->default('SINGLE');
            $table->date('release_date');
            $table->date('release_date_original')->nullable();
            $table->string('upc')->nullable();
            $table->string('cover')->nullable();
            $table->string('copyright', 250)->nullable()->default('labelmiraclestudioapps');
            $table->year('copyright_year')->nullable();
            $table->string('publisher', 100)->nullable()->default('labelmiraclestudioapps');
            $table->year('publisher_year')->nullable();
            $table->string('label', 250)->nullable();
            $table->tinyInteger('submit_status', false, false)->default(0);
            $table->enum('verification_status', ['PENDING','REJECTED','APPROVED'])->default('PENDING');
            $table->text('description')->nullable();
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
