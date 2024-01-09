<?php

use App\Enums\FoodStatus;
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
        Schema::create('food', function (Blueprint $table) {
            $table->id();
            $table->string('name_food', 255);
            $table->text('food_description');
            $table->text('beverages_description');
            /** $table->text('description') */
            $table->enum('status', array_column(FoodStatus::cases(), 'name'));
            $table->float('price');
            $table->string('slug')->unique();
            $table->foreignId('buffet')->constrained(
                table: 'buffets', indexName: 'foods_buffet_id'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foods');
    }
};
