<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryMapping extends Model
{
    protected $fillable = ['category_id', 'integration_category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function integrationCategory()
    {
        return $this->belongsTo(IntegrationCategory::class);
    }
}