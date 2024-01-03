<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\BuffetStatus; 

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('buffets', function (Blueprint $table) {
            $table->id();
            $table->string('trading_name');
            $table->string('email')->unique();
            $table->string('document')->unique();
            $table->string('slug')->unique();
            $table->foreignId('phone1')->nullable()->constrained(
                table: 'phones', indexName: 'buffets_phone1'
            );
            $table->foreignId('phone2')->nullable()->constrained(
                table: 'phones', indexName: 'buffets_phone2'
            );
            $table->foreignId('address')->nullable()->constrained(
                table: 'addresses', indexName: 'buffets_address'
            );
            $table->enum('status', array_column(BuffetStatus::cases(), 'name'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buffets');
    }
};
