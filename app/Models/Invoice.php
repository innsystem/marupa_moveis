<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'integration_id', 'method_type', 'total', 'status', 'due_at', 'paid_at'];

    protected $dates = ['created_at', 'updated_at', 'due_at', 'paid_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function integration()
    {
        return $this->belongsTo(Integration::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status', 'id');
    }

    public function webhooks()
    {
        return $this->hasMany(Webhook::class);
    }

    public function latestWebhook()
    {
        return $this->hasOne(Webhook::class)->where('status', 23)->latest();
    }

    public function latestPaidWebhook()
    {
        return $this->hasOne(Webhook::class)->where('status', 24)->latest();
    }
    
    public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d/m/Y H:i') : null;
    }

    public function getDueAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d/m/Y') : null;
    }

    public function getPaidAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d/m/Y H:i') : null;
    }

    public function getStatusNameAttribute()
    {
        $status = Status::byId($this->status)->first();
        return $status ? $status->name : 'N/A';
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
