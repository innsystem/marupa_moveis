<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'slug', 
        'description', 
        'image',
        'status', 
        'sort_order',
        'is_recurring',
        'single_payment_price',
        'monthly_price',
        'quarterly_price',
        'semiannual_price',
        'annual_price',
        'biennial_price'
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
        'single_payment_price' => 'decimal:2',
        'monthly_price' => 'decimal:2',
        'quarterly_price' => 'decimal:2',
        'semiannual_price' => 'decimal:2',
        'annual_price' => 'decimal:2',
        'biennial_price' => 'decimal:2',
    ];

    /**
     * Verifica se o serviço tem pagamento único disponível
     */
    public function hasSinglePayment()
    {
        return !is_null($this->single_payment_price) && $this->single_payment_price > 0;
    }

    /**
     * Retorna todos os planos de pagamento recorrente disponíveis para este serviço
     */
    public function getAvailableRecurringPlans()
    {
        $plans = [];
        
        if ($this->is_recurring) {
            if (!is_null($this->monthly_price) && $this->monthly_price > 0) {
                $plans['monthly'] = [
                    'name' => 'Mensal',
                    'price' => $this->monthly_price
                ];
            }
            
            if (!is_null($this->quarterly_price) && $this->quarterly_price > 0) {
                $plans['quarterly'] = [
                    'name' => 'Trimestral',
                    'price' => $this->quarterly_price
                ];
            }
            
            if (!is_null($this->semiannual_price) && $this->semiannual_price > 0) {
                $plans['semiannual'] = [
                    'name' => 'Semestral',
                    'price' => $this->semiannual_price
                ];
            }
            
            if (!is_null($this->annual_price) && $this->annual_price > 0) {
                $plans['annual'] = [
                    'name' => 'Anual',
                    'price' => $this->annual_price
                ];
            }
            
            if (!is_null($this->biennial_price) && $this->biennial_price > 0) {
                $plans['biennial'] = [
                    'name' => 'Bienal',
                    'price' => $this->biennial_price
                ];
            }
        }
        
        return $plans;
    }
}
