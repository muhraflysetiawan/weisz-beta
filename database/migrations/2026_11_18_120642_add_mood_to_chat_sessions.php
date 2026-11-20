<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->tinyInteger('mood_score')->unsigned()->nullable()->after('status'); // 1=Very Difficult..5=Very Good
            $table->string('mood_label', 20)->nullable()->after('mood_score');         // 'very_difficult'...'very_good'
        });
    }

    public function down(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->dropColumn(['mood_score', 'mood_label']);
        });
    }
};
