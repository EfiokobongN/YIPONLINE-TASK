<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'compare_price',
        'stock', 'category_id', 'image', 'is_active', 'is_featured',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    // Auto-generate slug on create
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            $product->slug = $product->slug ?? Str::slug($product->name);
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFormattedPriceAttribute()
    {
        return '₦' . number_format($this->price, 2);
    }

    public function getDiscountPercentAttribute()
    {
        if ($this->compare_price && $this->compare_price > $this->price) {
            return round((($this->compare_price - $this->price) / $this->compare_price) * 100);
        }
        return null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category_id', $category);
    }

    public function isInStock()
    {
        return $this->stock > 0;
    }
}
