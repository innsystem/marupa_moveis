<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'integration_id',
        'type', // income (Entrada) - expense (Despesa)
        'amount',
        'gateway_fee',
        'description',
        'date',
    ];

    protected $casts = [
        'date' => 'datetime',
        'amount' => 'decimal:2',
        'gateway_fee' => 'decimal:2',
    ];

    /**
     * Relacionamento com a fatura (Invoice).
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class)->withDefault();
    }

    /**
     * Relacionamento com a integração de pagamento (Integration).
     */
    public function integration()
    {
        return $this->belongsTo(Integration::class)->withDefault();
    }

    /**
     * Calcula o valor líquido da transação (após taxas).
     */
    public function getNetAmountAttribute()
    {
        return $this->amount - $this->gateway_fee;
    }

    /**
     * Formata o valor da transação como moeda.
     */
    public function getFormattedAmountAttribute()
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }

    /**
     * Formata a taxa de gateway como moeda.
     */
    public function getFormattedGatewayFeeAttribute()
    {
        return 'R$ ' . number_format($this->gateway_fee, 2, ',', '.');
    }

    /**
     * Formata a data da transação.
     */
    public function getFormattedDateAttribute()
    {
        return $this->date->format('d/m/Y H:i');
    }
}
