<?php

use App\Enums\DayWeek;
use App\Enums\ScheduleStatus;
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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->enum('day_week', array_column(DayWeek::cases(),'name'));
            $table->time('start_time');
            $table->integer('duration');
            $table->date('start_block')->nullable();
            $table->date('end_block')->nullable();
            $table->enum('status', array_column(ScheduleStatus::cases(), 'name')); 
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
        Schema::dropIfExists('schedules');
    }
};
