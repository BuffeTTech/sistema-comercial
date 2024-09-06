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
        Schema::create('subscription_configurations', function (Blueprint $table) {
            $table->id();
            $table->integer('max_employees')->nullable()->default(0); // se for null é pq nao tem limite!
            $table->integer('max_food_photos')->nullable()->default(0); // se for null é pq nao tem limite!
            $table->integer('max_decoration_photos')->nullable()->default(0); // se for null é pq nao tem limite!
            $table->integer('max_recommendations')->nullable()->default(0); // se for null é pq nao tem limite!
            $table->integer('max_survey_questions')->nullable()->default(0); // se for null é pq nao tem limite!
            $table->foreignId('subscription_id')->constrained(
                table: 'subscriptions', indexName: 'subscription_configurations_subscriptions_id'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_configurations');
    }
};
