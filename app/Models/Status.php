<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'color',
        'icon'
    ];

    public static function default()
    {
        return self::byType('default');
    }

    public static function byType(string $type)
    {
        return self::where('type', $type)->get();
    }
    
    public static function byId(string $id)
    {
        return self::where('id', $id)->get();
    }

    public static function forInvoices()
    {
        return Status::whereIn('id', [23,24,26])->get();
    }
}
