<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('company')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->unique();
            $table->string('source')->nullable(); // formulario, importacion, manual, landing
            $table->json('tags')->nullable();
            $table->boolean('consent')->default(false);
            $table->timestamp('consent_at')->nullable();
            $table->boolean('subscribed')->default(true);
            $table->timestamp('unsubscribed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
