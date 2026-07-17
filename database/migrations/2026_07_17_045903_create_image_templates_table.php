<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('image_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('format'); // cuadrado, vertical, historia, horizontal, portada
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->string('background_type')->default('color'); // color, image, ai
            $table->string('background_value')->nullable(); // hex color o ruta de imagen
            $table->boolean('overlay_gradient')->default(true);
            $table->string('primary_color', 20)->default('#2563EB');
            $table->string('accent_color', 20)->default('#DC2626');
            $table->string('title_position')->default('center'); // top, center, bottom
            $table->boolean('show_price')->default(false);
            $table->boolean('show_qr')->default(false);
            $table->string('footer_text')->nullable();
            $table->boolean('is_master')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_templates');
    }
};
