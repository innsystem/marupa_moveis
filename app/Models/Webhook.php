<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'integration_id',
        'payment_code',
        'status',
        'response_json',
    ];

    protected $casts = [
        'response_json' => 'array',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function integration()
    {
        return $this->belongsTo(Integration::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status', 'id');
    }

    public function getStatusNameAttribute()
    {
        $status = Status::byId($this->status)->first();
        return $status ? $status->name : 'N/A';
    }
}
