<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Integration extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'type', 'settings', 'status'];

    protected $casts = [
        'settings' => 'array', // Permite trabalhar com JSON como array
    ];

    /**
     * Atualiza o cache quando uma integração é salva ou excluída.
     */
    protected static function booted()
    {
        static::saved(function ($integration) {
            Cache::forget('integrations');
        });

        static::deleted(function ($integration) {
            Cache::forget('integrations');
        });
    }

    /**
     * Obter uma configuração específica da integração.
     */
    public function getSetting($slug)
    {
        return $this->settings[$slug] ?? null;
    }

    public function getTypeTranslationAttribute()
    {
        switch ($this->type) {
            case 'communication':
                return 'Comunicação';
            case 'marketplaces':
                return 'Marketplaces';
            case 'analytics':
                return 'Análise';
            case 'crm':
                return 'CRM';
            case 'payments':
                return 'Pagamentos';
            case 'projects':
                return 'Projetos';
            case 'ecommerce':
                return 'E';
            case 'calendar':
                return 'Calendários';
            case 'finance':
                return 'Finanças';
            case 'storage':
                return 'Armazenamento';
            case 'utilities':
                return 'Utilitários';
            default:
                return $this->type; // Retorna o valor original se não encontrar uma correspondência
        }
    }

    public function integrationCategories()
    {
        return $this->hasMany(IntegrationCategory::class)->orderBy('api_category_name', 'ASC');
    }
}
