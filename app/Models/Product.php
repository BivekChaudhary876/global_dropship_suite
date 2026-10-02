<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'category_id', 'name', 'slug',
        'description', 'price', 'stock_quantity', 'image_path',
    ];

    protected function casts(): array
    {
        return ['price' => 'decimal:2'];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    

    public function tags()
    {
        return $this->belongsToMany(Tag::class); // many-to-many via product_tag pivot
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->oldest('id');
    }
}