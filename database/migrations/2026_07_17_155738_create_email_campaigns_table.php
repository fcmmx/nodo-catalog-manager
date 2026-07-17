<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('newsletter');
            $table->string('subject');
            $table->string('from_name');
            $table->string('from_email');
            $table->foreignId('contact_list_id')->nullable()->constrained('contact_lists')->nullOnDelete();
            $table->json('blocks')->nullable(); // constructor visual: encabezado, texto, imagen, botón, productos, separador, redes, pie legal
            $table->string('status')->default('borrador'); // borrador, programada, enviando, enviada, pausada
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('open_count')->default(0);
            $table->unsignedInteger('click_count')->default(0);
            $table->unsignedInteger('bounce_count')->default(0);
            $table->unsignedInteger('unsubscribe_count')->default(0);
            $table->unsignedInteger('batch_limit')->default(50); // límite de envíos por ejecución de cron
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_campaigns');
    }
};
