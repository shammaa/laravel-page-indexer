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
        Schema::create('page_indexer_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('page_indexer_pages')->onDelete('cascade');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending')->comment('Job status');
            $table->string('search_engine', 50)->comment('Search engine name (google, bing, yandex, etc.)');
            $table->json('request_data')->nullable()->comment('Request payload sent to API');
            $table->json('response_data')->nullable()->comment('API response data');
            $table->text('error_message')->nullable()->comment('Error message if failed');
            $table->timestamp('processed_at')->nullable()->comment('Processing completion timestamp');
            $table->timestamps();
            
            $table->index(['page_id', 'status']);
            $table->index(['search_engine', 'status']);
            $table->index('processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_indexer_jobs');
    }
};

