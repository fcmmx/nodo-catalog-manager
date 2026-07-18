<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_audits', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('status')->default('completado'); // completado, error
            $table->unsignedTinyInteger('score')->nullable();
            $table->unsignedTinyInteger('seo_score')->nullable();
            $table->unsignedTinyInteger('aeo_score')->nullable();
            $table->unsignedTinyInteger('geo_score')->nullable();
            $table->json('results')->nullable();
            $table->text('error_message')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_audits');
    }
};
