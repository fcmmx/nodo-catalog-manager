<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_campaign_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_campaign_id')->constrained('email_campaigns')->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->string('token', 64)->unique(); // usado en píxel de apertura, enlaces y baja
            $table->string('status')->default('pendiente'); // pendiente, enviado, rebotado, error
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->unique(['email_campaign_id', 'contact_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_campaign_sends');
    }
};
