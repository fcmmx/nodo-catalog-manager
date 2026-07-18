<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commerce_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // feed_csv, feed_xml, connection_test
            $table->string('status'); // exitoso, error
            $table->unsignedInteger('products_count')->nullable();
            $table->text('message')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commerce_sync_logs');
    }
};
