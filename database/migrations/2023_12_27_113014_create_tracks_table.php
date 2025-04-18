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
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->string('title', 250);
            $table->string('file');
            $table->string('ISRC');
            $table->string('version', 200)->default('original');
            $table->enum('vocal', ['YES', 'NO'])->default('YES');
            $table->integer('preview')->unsigned()->nullable();
            $table->string('explicit_content', 100);
            $table->string('genre', 100);
            $table->string('lyric_language', 200)->nullable();
            $table->longText('lyrics')->nullable();
            $table->foreignId('distribution_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};
