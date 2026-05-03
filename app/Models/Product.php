<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'slug',
        'price',
        'cost_price',
        'stock',
        'sku',
        'category',
        'featured_image',
        'status',
        'sold',
        'meta_description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
    ];

    /**
     * Get the user that owns the product.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cart items for this product.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (!$product->slug) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    /**
     * Get the route key for implicit model binding.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Check if product is available
     */
    public function isAvailable()
    {
        return $this->status === 'active' && $this->stock > 0;
    }

    /**
     * Get profit margin
     */
    public function getProfitMargin()
    {
        if (!$this->cost_price) return null;
        return (($this->price - $this->cost_price) / $this->price) * 100;
    }

    /**
     * Format price for display
     */
    public function getFormattedPrice()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
