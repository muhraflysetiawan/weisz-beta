<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RuleAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'expert_rule_id',
        'action_type',
        'action_payload',
    ];

    protected function casts(): array
    {
        return [
            'action_payload' => 'array',
        ];
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(ExpertRule::class, 'expert_rule_id');
    }
}
