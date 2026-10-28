<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpertRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'name',
        'description',
        'priority',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'priority' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ProblemTopic::class, 'topic_id');
    }

    public function conditions(): HasMany
    {
        return $this->hasMany(RuleCondition::class, 'expert_rule_id');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(RuleAction::class, 'expert_rule_id');
    }
}
