<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('slug')->unique();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('collection_id')->nullable()->constrained('collections')->nullOnDelete();
            $table->enum('type', ['producto', 'servicio'])->default('servicio');

            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->longText('benefits')->nullable();
            $table->longText('features')->nullable();

            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('old_price', 12, 2)->nullable();
            $table->string('currency', 3)->default('MXN');
            $table->string('pricing_model')->nullable();
            $table->string('price_prefix_text')->nullable();
            $table->boolean('tax_included')->default(true);

            $table->enum('availability', ['disponible', 'agotado', 'bajo_pedido', 'proximamente'])->default('disponible');
            $table->enum('status', ['borrador', 'activo', 'inactivo', 'archivado'])->default('borrador');

            $table->string('main_image')->nullable();
            $table->string('video_url')->nullable();
            $table->string('url')->nullable();
            $table->string('demo_url')->nullable();
            $table->string('whatsapp_url')->nullable();
            $table->text('whatsapp_message')->nullable();

            $table->json('tags')->nullable();
            $table->text('keywords')->nullable();
            $table->text('seo_text')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->json('structured_data')->nullable();

            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'type']);
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
