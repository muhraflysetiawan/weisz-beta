<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ProblemCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(ProblemSubcategory::class, 'category_id')->orderBy('sort_order');
    }

    public function topics(): HasManyThrough
    {
        return $this->hasManyThrough(ProblemTopic::class, ProblemSubcategory::class, 'category_id', 'subcategory_id');
    }

    public function knowledgeBases(): HasMany
    {
        return $this->hasMany(KnowledgeBase::class, 'category_id');
    }

    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class, 'category_id');
    }
}
