<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Workflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'workflow_json',
        'tags',
        'visibility',
        'user_id',
        'slug',
    ];

    protected $casts = [
        'workflow_json' => 'array',
        'tags' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($workflow) {
            if (empty($workflow->slug)) {
                $workflow->slug = Str::slug($workflow->title . '-' . Str::random(6));
            }
        });
    }

    public function getDescriptionExcerpt(int $limit = 100): string
    {
        if (!$this->description) {
            return '';
        }

        $text = strip_tags($this->description);
        return Str::limit($text, $limit);
    }

    public function scopeVisible($query, ?User $user = null)
    {
        if (!$user) {
            return $query->where('visibility', 'public');
        }

        return $query->where(function ($q) use ($user) {
            $q->where('visibility', 'public')
              ->orWhere('user_id', $user->id)
              ->orWhere('visibility', 'team'); // Pour plus tard si vous ajoutez la notion d'équipe
        });
    }

    public function scopeWithFilters($query, array $filters = [])
    {
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['tags'])) {
            $tags = is_array($filters['tags']) ? $filters['tags'] : [$filters['tags']];
            foreach ($tags as $tag) {
                $query->whereJsonContains('tags', $tag);
            }
        }

        if (!empty($filters['author'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['author'] . '%');
            });
        }

        return $query;
    }
}
