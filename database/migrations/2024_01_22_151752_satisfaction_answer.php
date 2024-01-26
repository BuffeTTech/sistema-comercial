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
        Schema::create('satisfaction_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained(
                table: 'satisfaction_questions', indexName:'answer_question_id')->onDelete('cascade');
            $table->foreignId('booking_id')->constrained(
                table:'bookings', indexName: 'answer_booking_id')->onDelete('cascade');
            $table->text('answer'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

