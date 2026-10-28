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
        Schema::create('expert_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->nullable()->constrained('problem_topics')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('rule_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expert_rule_id')->constrained('expert_rules')->cascadeOnDelete();
            $table->string('fact_key');
            $table->string('operator')->default('==');
            $table->string('fact_value');
            $table->timestamps();
        });

        Schema::create('rule_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expert_rule_id')->constrained('expert_rules')->cascadeOnDelete();
            $table->string('action_type');
            $table->json('action_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rule_actions');
        Schema::dropIfExists('rule_conditions');
        Schema::dropIfExists('expert_rules');
    }
};
