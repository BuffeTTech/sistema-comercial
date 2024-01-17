<?php

use App\Enums\BookingStatus;
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
            $table->string('name_birthdayperson', 255);
            $table->string('years_birthdayperson', 255);
            $table->integer('num_guests'); 
            $table->date('party_day', 0);
            $table->foreignId('buffet_id')->constrained(
                table: 'buffets', indexName: 'booking_buffet_id'
            );
            $table->foreignId('user_id')->constrained(
                table: 'users', indexName: 'bookings_user_id'
            );

            $table->foreignId('food_id')->constrained(
                table: 'foods', indexName: 'booking_food_id'
            );
            $table->float('price_food'); 

            $table->foreignId('decoration_id')->constrained(
                table: 'decorations', indexName: 'booking_decoration_id'
            );
            $table->float('price_decoration'); 
            
            $table->foreignId('schedule_id')->constrained(
                table: 'schedules', indexName: 'bookings_schedule_id'
            );
            $table->float('price_schedule'); 

            $table->float('discount');
            $table->enum('status', array_column(BookingStatus::cases(), 'name'));
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
