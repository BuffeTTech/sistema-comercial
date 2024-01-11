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
        Schema::create('buffet_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buffet_id')->constrained(
                table: 'buffets', indexName: 'buffet_subscriptions_buffet_id'
            );
            $table->foreignId('subscription_id')->constrained(
                table: 'subscriptions', indexName: 'buffet_subscriptions_subscriptions_id'
            );

            // Informações de pagamento
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buffet_subscriptions');
    }
};
