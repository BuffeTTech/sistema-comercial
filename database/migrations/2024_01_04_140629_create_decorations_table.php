<?php

use App\Enums\DecorationStatus;
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
        Schema::create('decorations', function (Blueprint $table) {
            $table->id();
            $table->string('main_theme');
            $table->string('slug');
            $table->text('description');
            $table->float('price');
            $table->enum('status', array_column(DecorationStatus::cases(),'name'));
            $table->foreignId('buffet')->constrained(
                table: 'buffets', indexName:'decoration_buffet_id'
            );
            //$table->string('photo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decorations');
    }
};
