<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'slug',
        'thumbnail',
        'status',
        'photo_count',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Get the user that owns the album.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the photos for the album.
     */
    public function photos()
    {
        return $this->hasMany(Photo::class)->orderBy('order');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($album) {
            if (! $album->slug) {
                $album->slug = static::uniqueSlug($album->title);
            }
        });
    }

    /**
     * Generate slug yang dijamin unik (judul sama tidak akan error).
     */
    protected static function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }

    /**
     * Get the route key for implicit model binding.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Check if album is published
     */
    public function isPublished()
    {
        return $this->status === 'published' && $this->published_at !== null;
    }
}
