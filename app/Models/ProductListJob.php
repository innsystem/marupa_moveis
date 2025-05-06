<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductListJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_data',
        'status',
        'scheduled_at',
    ];

    protected $casts = [
        'product_data' => 'array',
        'scheduled_at' => 'datetime',
    ];
}
