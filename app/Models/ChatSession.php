<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'subcategory_id',
        'topic_id',
        'title',
        'status',
        'started_at',
        'ended_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProblemCategory::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(ProblemSubcategory::class, 'subcategory_id');
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ProblemTopic::class, 'topic_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'session_id')->orderBy('id');
    }

    public function context(): HasOne
    {
        return $this->hasOne(ConversationContext::class, 'session_id');
    }

    public function summary(): HasOne
    {
        return $this->hasOne(ConversationSummary::class, 'session_id');
    }

    public function riskAssessments(): HasMany
    {
        return $this->hasMany(RiskAssessment::class, 'session_id');
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(SessionRecommendation::class, 'session_id');
    }

    /**
     * Check if user has sent any message in this session.
     */
    public function hasUserMessages(): bool
    {
        return $this->messages()->where('sender', 'user')->exists();
    }

    /**
     * Scope a query to only include sessions where user actually participated with at least one message.
     *
     * @param  Builder<ChatSession>  $query
     * @return Builder<ChatSession>
     */
    public function scopeWithUserMessages($query)
    {
        return $query->whereHas('messages', function ($q) {
            $q->where('sender', 'user');
        });
    }
}
