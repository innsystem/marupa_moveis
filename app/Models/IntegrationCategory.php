<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegrationCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'integration_id',
        'category_id',
        'api_category_id',
        'api_category_name',
        'api_category_link_affiliate',
        'api_category_commission',
    ];

    public function integration()
    {
        return $this->belongsTo(Integration::class);
    }

    public function parent()
    {
        return $this->belongsTo(IntegrationCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(IntegrationCategory::class, 'parent_id');
    }
    
}
