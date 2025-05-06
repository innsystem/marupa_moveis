<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'thumb', 'parent_id', 'description', 'status'];

    // Relacionamento many-to-many com produtos
    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }

    public function randomProduct()
    {
        return $this->products()->inRandomOrder()->first();
    }

    public function productAffiliateLinks()
    {
        return $this->products()->with('affiliateLinks');
    }

    public function productCount()
    {
        return $this->products()->count();
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
