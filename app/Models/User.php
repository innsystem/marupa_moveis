<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['user_group_id', 'name', 'email', 'password', 'password_code', 'document', 'ddi', 'phone', 'remember_token'];

    public function group()
    {
        return $this->belongsTo(UserGroup::class, 'user_group_id');
    }

    public function hasPermission($key)
    {
        // Carrega todas as permissões do grupo do usuário em cache
        $permissions = cache()->remember("user_permissions_{$this->id}", now()->addMinutes(240), function () {
            return $this->group->permissions->pluck('key')->toArray(); // Carrega apenas as chaves
        });

        // Verifica se a permissão está presente no cache
        return in_array($key, $permissions);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function address()
    {
        return $this->hasOne(UserAddress::class)->where('is_default', 1)->first();
    }

    public function services()
    {
        return $this->hasMany(UserService::class);
    }

    public function preferences()
    {
        return $this->hasOne(UserPreference::class);
    }

    public function getPhoneDdiAttribute()
    {
        $ddi = trim($this->ddi ?? '');
        $phone = trim($this->phone ?? '');
        return trim($ddi . ' ' . $phone);
    }
}
