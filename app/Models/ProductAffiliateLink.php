<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAffiliateLink extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'integration_id', 'affiliate_link', 'clicks', 'api_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relacionamento com a integração (marketplace)
    public function integration()
    {
        return $this->belongsTo(Integration::class);
    }
}
