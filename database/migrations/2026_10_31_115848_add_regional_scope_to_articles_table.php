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
        Schema::table('articles', function (Blueprint $table) {
            $table->string('target_region_scope', 16)->nullable()->index();
            $table->string('target_province_slug', 191)->nullable()->index();
            $table->string('target_district_slug', 191)->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex(['target_region_scope']);
            $table->dropIndex(['target_province_slug']);
            $table->dropIndex(['target_district_slug']);
            $table->dropColumn(['target_region_scope', 'target_province_slug', 'target_district_slug']);
        });
    }
};
