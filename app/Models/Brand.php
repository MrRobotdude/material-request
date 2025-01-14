<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $primaryKey = 'brand_code'; // Primary key adalah brand_code
    public $incrementing = false; // Non-incremental key
    protected $keyType = 'string'; // Tipe data primary key adalah string

    protected $fillable = [
        'brand_code',
        'brand_name',
        'brand_initial',
        'is_active',
    ];

    public static function generateBrandCode()
    {
        $lastBrand = self::orderBy('brand_code', 'desc')->first();
        $nextNumber = $lastBrand ? ((int) substr($lastBrand->brand_code, 4)) + 1 : 1;
        return 'MRK-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'brand_product_specification', 'brand_code', 'product_code', 'brand_code', 'product_code')
            ->withPivot('specification_id')
            ->withTimestamps();
    }

    public function specifications()
    {
        return $this->belongsToMany(Specification::class, 'brand_product_specification', 'brand_code', 'specification_id', 'brand_code', 'id')
            ->withPivot('product_code')
            ->withTimestamps();
    }
}
