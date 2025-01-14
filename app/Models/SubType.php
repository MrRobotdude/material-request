<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubType extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'sub_types';
    protected $primaryKey = 'sub_type_code';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'sub_type_code',
        'sub_type_name',
        'type_code',
        'initial',
        'is_active',
    ];

    public static function generateSubTypeCode()
    {
        $latest = self::latest('sub_type_code')->first();
        $number = $latest ? (int) substr($latest->sub_type_code, 4) + 1 : 1;
        return 'STP-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_code', 'type_code');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'sub_type_code', 'sub_type_code');
    }
}
