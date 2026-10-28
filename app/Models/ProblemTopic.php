<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProblemTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'subcategory_id',
        'name',
        'slug',
        'description',
        'prompt_context',
        'suggested_chips',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'suggested_chips' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(ProblemSubcategory::class, 'subcategory_id');
    }

    public function knowledgeBases(): HasMany
    {
        return $this->hasMany(KnowledgeBase::class, 'topic_id');
    }

    public function expertRules(): HasMany
    {
        return $this->hasMany(ExpertRule::class, 'topic_id')->orderByDesc('priority');
    }

    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class, 'topic_id');
    }
}
