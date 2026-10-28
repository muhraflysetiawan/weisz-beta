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
        Schema::create('knowledge_bases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('problem_categories')->nullOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained('problem_subcategories')->nullOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained('problem_topics')->nullOnDelete();
            $table->string('title');
            $table->text('content');
            $table->string('reference_source')->nullable();
            $table->string('status')->default('published');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_bases');
    }
};
