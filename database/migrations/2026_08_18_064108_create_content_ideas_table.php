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
        Schema::create('content_ideas', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('suggestion')->nullable();
            $table->string('type')->default('idea');
            $table->date('event_date')->nullable();
            $table->string('tmdb_ref')->nullable();
            $table->enum('status', ['new', 'planned', 'published', 'dismissed'])->default('new');
            $table->text('notes')->nullable();
            $table->foreignId('post_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_ideas');
    }
};
