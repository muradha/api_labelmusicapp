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
            $table->string('title', 250)->unique();
            $table->string('file');
            $table->string('ISRC');
            $table->string('version', 200);
            $table->enum('vocal', ['YES', 'NO'])->default('YES');
            $table->integer('preview')->unsigned()->nullable();
            $table->string('lyric_language', 200)->nullable();
            $table->integer('size')->unsigned()->nullable();
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
