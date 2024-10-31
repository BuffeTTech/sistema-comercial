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
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('min_days_booking')->default(30);
            $table->unsignedTinyInteger('min_days_update_booking')->default(30);
            $table->unsignedTinyInteger('max_days_unavaiable_booking')->default(7);
            $table->string('buffet_instagram')->nullable();
            $table->string('buffet_linkedin')->nullable();
            $table->string('buffet_facebook')->nullable();
            $table->string('buffet_whatsapp')->nullable();
            $table->boolean('external_decoration')->default(false);
            $table->boolean('charge_by_schedule')->default(false);
            $table->boolean('allow_post_payment')->default(false);
            $table->boolean('children_affect_pricing')->default(false);
            $table->double('children_price_adjustment')->nullable();
            $table->unsignedTinyInteger('child_age_limit')->nullable();
            $table->foreignId('buffet_id')->constrained(
                table:"buffets",indexName:'configuration_buffet_id'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
