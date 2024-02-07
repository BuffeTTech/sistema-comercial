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
        Schema::table('buffets', function (Blueprint $table) {
            $table->foreignId('logo_id')->nullable()->constrained(
                table: 'buffet_photos', indexName: 'buffets_logo_id'     
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buffets', function (Blueprint $table) {
            // Reverta as alterações feitas no método up
            $table->dropColumn('logo_id');
        });
    }
};
