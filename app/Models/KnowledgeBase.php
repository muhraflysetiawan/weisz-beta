<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeBase extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'topic_id',
        'title',
        'content',
        'reference_source',
        'status',
    ];

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
}
