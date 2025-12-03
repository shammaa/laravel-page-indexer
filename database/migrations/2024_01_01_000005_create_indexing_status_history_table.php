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
        Schema::create('page_indexer_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('page_indexer_pages')->onDelete('cascade');
            $table->enum('status', ['pending', 'submitted', 'indexed', 'failed'])->comment('Status at this point');
            $table->string('search_engine', 50)->nullable()->comment('Search engine that reported this status');
            $table->json('metadata')->nullable()->comment('Additional status metadata');
            $table->timestamp('checked_at')->comment('When this status was checked');
            $table->timestamps();
            
            $table->index(['page_id', 'checked_at']);
            $table->index('checked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_indexer_status_history');
    }
};

