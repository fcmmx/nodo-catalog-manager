<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('status')->default('borrador'); // borrador, publicada, archivada

            // Hero
            $table->string('headline');
            $table->string('subheadline')->nullable();
            $table->string('hero_image_path')->nullable();

            // Constructor de secciones (problema, solución, beneficios, testimonios, FAQ, CTA, producto, formulario)
            $table->json('sections')->nullable();

            // Llamada a la acción principal
            $table->string('cta_text')->default('Quiero más información');
            $table->string('cta_whatsapp_number')->nullable();
            $table->string('cta_whatsapp_message')->nullable();
            $table->string('cta_url')->nullable();

            // SEO / Open Graph / datos estructurados
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('og_image_path')->nullable();
            $table->json('structured_data')->nullable();

            // Analítica (opcional, por landing)
            $table->string('ga4_id')->nullable();
            $table->string('meta_pixel_id')->nullable();
            $table->string('gtm_id')->nullable();

            // Captura de prospectos
            $table->boolean('capture_form_enabled')->default(true);
            $table->foreignId('contact_list_id')->nullable()->constrained('contact_lists')->nullOnDelete();

            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('leads_count')->default(0);

            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
