<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationContext extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'current_state',
        'signals',
        'context_data',
        'current_mood',
        'initial_mood',
        'mood_history',
    ];

    protected function casts(): array
    {
        return [
            'signals' => 'array',
            'context_data' => 'array',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class, 'session_id');
    }

    public function getCurrentMoodAttribute(): ?string
    {
        $contextData = $this->context_data ?? [];
        $signals = $this->signals ?? [];

        return $contextData['current_mood'] ?? $signals['user_mood'] ?? null;
    }

    public function setCurrentMoodAttribute(?string $value): void
    {
        $contextData = $this->context_data ?? [];
        $contextData['current_mood'] = $value;
        $this->attributes['context_data'] = json_encode($contextData);

        $signals = $this->signals ?? [];
        $signals['user_mood'] = $value;
        $this->attributes['signals'] = json_encode($signals);
    }

    public function getInitialMoodAttribute(): ?string
    {
        $contextData = $this->context_data ?? [];

        return $contextData['initial_mood'] ?? null;
    }

    public function setInitialMoodAttribute(?string $value): void
    {
        $contextData = $this->context_data ?? [];
        $contextData['initial_mood'] = $value;
        $this->attributes['context_data'] = json_encode($contextData);
    }

    public function getMoodHistoryAttribute(): array
    {
        $contextData = $this->context_data ?? [];

        return $contextData['mood_history'] ?? [];
    }

    public function setMoodHistoryAttribute(array $value): void
    {
        $contextData = $this->context_data ?? [];
        $contextData['mood_history'] = $value;
        $this->attributes['context_data'] = json_encode($contextData);
    }
}
