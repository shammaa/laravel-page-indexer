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
        Schema::create('page_indexer_sites', function (Blueprint $table) {
            $table->id();
            $table->string('google_site_url')->unique()->comment('Site URL from Google Search Console');
            $table->string('name')->comment('Display name for the site');
            $table->boolean('auto_indexing_enabled')->default(false)->comment('Enable automatic indexing');
            $table->text('google_access_token')->nullable()->comment('Google OAuth access token');
            $table->text('google_refresh_token')->nullable()->comment('Google OAuth refresh token');
            $table->timestamp('google_token_expires_at')->nullable()->comment('Token expiration timestamp');
            $table->string('indexnow_api_key', 64)->nullable()->comment('IndexNow API key');
            $table->json('settings')->nullable()->comment('Additional site settings');
            $table->timestamps();
            
            $table->index('auto_indexing_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_indexer_sites');
    }
};

