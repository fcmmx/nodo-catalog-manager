<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('channel'); // facebook, instagram, linkedin, tiktok, x, google_business
            $table->string('label'); // nombre visible de la cuenta, ej. "NODO 360 - Página oficial"
            $table->string('external_account_id')->nullable();
            $table->text('access_token')->nullable(); // cifrado
            $table->timestamp('token_expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
