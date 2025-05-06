<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'description', 'status', 'location', 'type', 'sort_order'];

    public function images()
    {
        return $this->hasMany(PortfolioImage::class);
    }

    public function getFeaturedImageAttribute()
    {
        $featuredImage = $this->images()->where('featured', 1)->first();
        if ($featuredImage) {
            return asset('storage/' . $featuredImage->image_path);
        }

        return asset('galerias/avatares/sem_foto.jpg');
    }
}
