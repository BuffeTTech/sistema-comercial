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
        Schema::create('buffet_photos', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_extension');
            $table->string('mime_type'); // "image/jpeg", "image/png"
            $table->unsignedBigInteger('file_size');
            $table->foreignId('buffet_id')->constrained(
                table: 'buffets', indexName: 'buffet_photos_buffet_id'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buffet_photos');
    }
};