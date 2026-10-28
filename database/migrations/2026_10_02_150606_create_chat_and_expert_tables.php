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
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('problem_categories')->cascadeOnDelete();
            $table->foreignId('subcategory_id')->constrained('problem_subcategories')->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained('problem_topics')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('status')->default('active'); // active, completed, closed
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('chat_sessions')->cascadeOnDelete();
            $table->string('sender'); // user, assistant, system
            $table->text('message');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('conversation_contexts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('chat_sessions')->cascadeOnDelete()->unique();
            $table->string('current_state')->default('START');
            $table->json('signals')->nullable();
            $table->json('context_data')->nullable();
            $table->timestamps();
        });

        Schema::create('conversation_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('chat_sessions')->cascadeOnDelete()->unique();
            $table->string('main_topic');
            $table->text('user_shared');
            $table->text('possible_factors');
            $table->text('suggested_steps');
            $table->text('professional_support')->nullable();
            $table->text('summary_text')->nullable();
            $table->timestamps();
        });

        Schema::create('risk_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('chat_sessions')->cascadeOnDelete();
            $table->text('trigger_text');
            $table->string('risk_level')->default('critical');
            $table->text('safety_action');
            $table->boolean('is_acknowledged')->default(false);
            $table->timestamp('triggered_at');
            $table->timestamps();
        });

        Schema::create('session_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('chat_sessions')->cascadeOnDelete();
            $table->foreignId('professional_id')->constrained('professionals')->cascadeOnDelete();
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_recommendations');
        Schema::dropIfExists('risk_assessments');
        Schema::dropIfExists('conversation_summaries');
        Schema::dropIfExists('conversation_contexts');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_sessions');
    }
};
