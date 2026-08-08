<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'album_id',
        'user_id',
        'title',
        'description',
        'image_path',
        'image_url',
        'mime_type',
        'file_size',
        'width',
        'height',
        'order',
    ];

    /**
     * Get the album that owns the photo.
     */
    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    /**
     * Get the user that owns the photo.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full image URL
     */
    public function getImageUrlAttribute($value)
    {
        if ($value && str_starts_with($value, 'http')) {
            return $value;
        }

        // Path relatif disimpan di DB, URL penuh dibuat saat request (ikut APP_URL server)
        return asset($value);
    }

    /**
     * Delete photo file when deleting model
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($photo) {
            if ($photo->image_path && file_exists(public_path($photo->image_path))) {
                unlink(public_path($photo->image_path));
            }
        });
    }
}
