<?php

use App\Enums\GuestStatus;
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
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('document');
            $table->integer('age');
            $table->foreignId('booking_id')->constrained(
                table:"bookings",indexName:'guests_booking_id'
            );
            $table->foreignId('buffet_id')->constrained(
                table:"buffets",indexName:'guests_buffet_id'
            );
            $table->enum('status', array_column(GuestStatus::cases(),'name'));

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
