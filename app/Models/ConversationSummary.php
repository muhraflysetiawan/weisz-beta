<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'main_topic',
        'user_shared',
        'possible_factors',
        'suggested_steps',
        'professional_support',
        'summary_text',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class, 'session_id');
    }
}
