<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\QuestionType; 
use App\Enums\SatisfactionQuestionStatus; 

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('satisfaction_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->enum('status', array_column(SatisfactionQuestionStatus::cases(), 'name'));
            $table->integer('answers')->default(0); // quantidade de respostas
            $table->enum('question_type', array_column(QuestionType::cases(), 'name'));
            $table->foreignId('buffet_id')->constrained(
                table: 'buffets', indexName: 'question_buffet_id'
            );
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
