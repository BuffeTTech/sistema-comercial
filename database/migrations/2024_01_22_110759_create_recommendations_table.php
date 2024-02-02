<?php

use App\Enums\RecommendationStatus;
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
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            //$table->string('title');
            $table->text('content');
            $table->enum('status', array_column(RecommendationStatus::cases(), 'name'));
            $table->foreignId('buffet_id')->constrained(
                table: 'buffets', indexName: "recommendation_buffet_id"
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
