<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('social_account_id')->nullable()->constrained('social_accounts')->nullOnDelete();
            $table->string('channel'); // facebook, instagram, linkedin, tiktok, x, google_business
            $table->text('content');
            $table->string('image_path')->nullable();
            $table->string('video_path')->nullable();
            $table->string('hashtags')->nullable();
            $table->string('link')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->string('timezone')->default('America/Mexico_City');
            $table->string('status')->default('borrador');
            // borrador, programada, enviando, enviada, pendiente_autorizacion, error, publicada_manual, cancelada
            $table->string('result')->nullable();
            $table->string('external_post_id')->nullable();
            $table->text('error_message')->nullable();
            $table->foreignId('duplicated_from')->nullable()->constrained('social_posts')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'scheduled_at']);
            $table->index('channel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_posts');
    }
};
