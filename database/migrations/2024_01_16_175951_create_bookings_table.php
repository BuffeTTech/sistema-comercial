<?php

use App\Enums\BookingStatus;
use App\Enums\DayTimePreference;
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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // etapa 1
            $table->string('name_birthdayperson', 255);
            $table->string('years_birthdayperson', 255);
            $table->date('birthday_date', 0);

            // etapa 2
            $table->integer('num_guests'); 
            $table->date('party_day', 0);

            $table->foreignId('food_id')->constrained(
                table: 'foods', indexName: 'booking_food_id'
            );
            $table->boolean('external_food')->default(false);
            $table->boolean('dietary_restrictions')->default(false);
            
            $table->float('price_food'); 

            $table->foreignId('decoration_id')->constrained(
                table: 'decorations', indexName: 'booking_decoration_id'
            );
            $table->boolean('external_decoration')->default(false);
            $table->float('price_decoration'); 
            
            $table->foreignId('schedule_id')->constrained(
                table: 'schedules', indexName: 'bookings_schedule_id'
            );
            $table->float('price_schedule'); 

            $table->text('final_notes')->nullable();

            $table->float('discount')->nullable();
            $table->enum('status', array_column(BookingStatus::cases(), 'name'));
            $table->json('daytime_preference');

            // Obrigatórios
            $table->foreignId('buffet_id')->constrained(
                table: 'buffets', indexName: 'booking_buffet_id'
            );
            $table->foreignId('user_id')->constrained(
                table: 'users', indexName: 'bookings_user_id'
            );
            $table->text('additional_food_observations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
