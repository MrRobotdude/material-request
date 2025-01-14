<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $primaryKey = 'type_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['type_code', 'type_name', 'initial', 'is_active'];

    public function subTypes()
    {
        return $this->hasMany(SubType::class, 'type_code', 'type_code')->withTimestamps();
    }

    public static function generateTypeCode()
    {
        $latest = self::latest('type_code')->first();
        $number = $latest ? (int) substr($latest->type_code, 4) + 1 : 1;
        return 'TYP-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
