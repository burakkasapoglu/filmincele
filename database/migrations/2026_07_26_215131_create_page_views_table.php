<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('route_name')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->integer('movie_id')->nullable();
            $table->timestamps();
            $table->index('created_at');
            $table->index(['url', 'created_at']);
        });
    }
};
