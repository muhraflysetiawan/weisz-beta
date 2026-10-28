<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Professional extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'profession',
        'specialization',
        'location',
        'address',
        'phone',
        'email',
        'description',
        'status',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'avatar_url',
        'display_name',
        'degree',
        'specialization_tags',
    ];

    public function recommendations(): HasMany
    {
        return $this->hasMany(SessionRecommendation::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        $slug = Str::slug($this->name);

        if (file_exists(public_path('images/professionals/'.$slug.'.jpg'))) {
            return asset('images/professionals/'.$slug.'.jpg');
        }

        if (str_contains($slug, 'nadia') && file_exists(public_path('images/professionals/nadia-amanda.jpg'))) {
            return asset('images/professionals/nadia-amanda.jpg');
        }
        if (str_contains($slug, 'reyhan') && file_exists(public_path('images/professionals/reyhan-wicaksono.jpg'))) {
            return asset('images/professionals/reyhan-wicaksono.jpg');
        }
        if (str_contains($slug, 'farida') && file_exists(public_path('images/professionals/siti-farida.jpg'))) {
            return asset('images/professionals/siti-farida.jpg');
        }
        if (str_contains($slug, 'dimas') && file_exists(public_path('images/professionals/dimas-arya.jpg'))) {
            return asset('images/professionals/dimas-arya.jpg');
        }

        $cleanName = preg_replace('/^(dr\.|drg\.)\s*/i', '', $this->display_name);
        $palette = ['0f766e', '0369a1', '4338ca', '6d28d9', 'be185d', 'b45309', '15803d', '334155'];
        $bg = $palette[abs(crc32($this->name)) % count($palette)];

        return 'https://ui-avatars.com/api/?name='.urlencode($cleanName).'&background='.$bg.'&color=ffffff&size=200&bold=true';
    }

    public function getDisplayNameAttribute(): string
    {
        $parts = explode(',', $this->name, 2);

        return trim($parts[0] ?? $this->name);
    }

    public function getDegreeAttribute(): string
    {
        $parts = explode(',', $this->name, 2);

        return isset($parts[1]) ? trim($parts[1]) : '';
    }

    /**
     * @return list<string>
     */
    public function getSpecializationTagsAttribute(): array
    {
        if (empty($this->specialization)) {
            return [];
        }

        $raw = preg_split('/[,&]+/', $this->specialization) ?: [];
        $tags = [];
        foreach ($raw as $item) {
            $cleaned = trim($item);
            if ($cleaned !== '') {
                $tags[] = $cleaned;
            }
        }

        return $tags;
    }
}
