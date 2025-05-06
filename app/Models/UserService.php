<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserService extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'service_id', 
        'start_date', 
        'end_date', 
        'price', 
        'period', 
        'metadata', 
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
        'metadata' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function statusRelation()
    {
        return $this->belongsTo(Status::class, 'status');
    }

    public function getPeriodLabelAttribute()
    {
        $periods = [
            'monthly' => 'Mensal',
            'quarterly' => 'Trimestral',
            'semiannual' => 'Semestral',
            'annual' => 'Anual',
            'biennial' => 'Bienal',
            'once' => 'Pagamento Único'
        ];

        return $periods[$this->period] ?? $this->period;
    }

    public function getFormattedPriceAttribute()
    {
        return 'R$ ' . number_format($this->price, 2, ',', '.');
    }
} 