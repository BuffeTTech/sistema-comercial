<?php

use App\Enums\DayWeek;
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
        Schema::create('buffet_schedules', function (Blueprint $table) {
            $table->id();
            $table->enum('day_week', array_column(DayWeek::cases(),'name'));
            $table->boolean('opened');
            $table->time('start')->nullable();
            $table->time('end')->nullable();
            $table->foreignId('buffet_id')->constrained(
                table: 'buffets', indexName: 'buffet_schedules_buffet_id'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buffet_schedules');
    }
};
