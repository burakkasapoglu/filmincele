<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('watchlists', function (Blueprint $table) {
            $table->string('share_token', 32)->nullable()->unique()->after('is_public');
        });
        // Generate tokens for existing public lists
        \App\Models\Watchlist::where('is_public', true)->whereNull('share_token')->each(function ($list) {
            $list->update(['share_token' => Str::random(16)]);
        });
    }

    public function down(): void
    {
        Schema::table('watchlists', function (Blueprint $table) {
            $table->dropColumn('share_token');
        });
    }
};
