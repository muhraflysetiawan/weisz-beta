<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RuleCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'expert_rule_id',
        'fact_key',
        'operator',
        'fact_value',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(ExpertRule::class, 'expert_rule_id');
    }
}
