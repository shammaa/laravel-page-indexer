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
        Schema::create('page_indexer_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('page_indexer_sites')->onDelete('cascade');
            $table->text('url')->comment('Page URL to index');
            $table->enum('indexing_status', ['pending', 'submitted', 'indexed', 'failed'])->default('pending')->comment('Current indexing status');
            $table->timestamp('last_indexed_at')->nullable()->comment('Last indexing attempt timestamp');
            $table->enum('indexing_method', ['google', 'indexnow', 'both'])->default('both')->comment('Indexing method to use');
            $table->json('metadata')->nullable()->comment('Additional page metadata');
            $table->timestamps();
            
            $table->index(['site_id', 'indexing_status']);
            $table->index(['site_id', 'last_indexed_at']);
            $table->unique(['site_id', 'url'], 'unique_site_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_indexer_pages');
    }
};

