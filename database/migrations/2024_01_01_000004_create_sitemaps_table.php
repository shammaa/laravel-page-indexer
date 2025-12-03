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
        Schema::create('page_indexer_sitemaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('page_indexer_sites')->onDelete('cascade');
            $table->string('sitemap_url')->comment('Sitemap XML URL');
            $table->string('type', 50)->default('sitemap')->comment('sitemap or sitemapindex');
            $table->timestamp('last_checked_at')->nullable()->comment('Last check timestamp');
            $table->integer('page_count')->default(0)->comment('Number of pages found');
            $table->timestamps();
            
            $table->unique(['site_id', 'sitemap_url']);
            $table->index(['site_id', 'last_checked_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_indexer_sitemaps');
    }
};

