<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->string('service_slug')->nullable();
            $table->string('target_city_slug')->nullable();
            $table->text('image_prompt')->nullable();
            $table->index(['target_city_slug', 'service_slug'], 'articles_regional_target_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->dropIndex('articles_regional_target_index');
            $table->dropColumn(['service_slug', 'target_city_slug', 'image_prompt']);
        });
    }
};
