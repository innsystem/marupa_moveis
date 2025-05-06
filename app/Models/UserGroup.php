<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class, 'user_group_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'group_permissions');
    }

    public function getPermissionsCountAttribute()
    {
        return $this->permissions()->count();
    }
    
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($userGroup) {
            if ($userGroup->users()->count() > 0) {
                throw new \Exception('Não é possível excluir um grupo que está relacionado a um ou mais usuários.');
            }
        });
    }
}
