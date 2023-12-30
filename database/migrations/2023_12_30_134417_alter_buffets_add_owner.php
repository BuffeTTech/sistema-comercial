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
            $table->foreignId('owner_id')->nullable()->constrained(
                table: 'users', indexName: 'buffets_owner_id'     
            );
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nome_da_tabela', function (Blueprint $table) {
            // Reverta as alterações feitas no método up
            $table->dropColumn('owner_id');
        });

    }
};
