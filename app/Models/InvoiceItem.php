<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'target_type',
        'target_id',
        'description',
        'quantity',
        'price_unit',
        'price_total',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function target()
    {
        return $this->morphTo();
    }
}