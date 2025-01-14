<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $primaryKey = 'product_code'; // Primary key adalah product_code
    public $incrementing = false; // Non-incremental key
    protected $keyType = 'string'; // Tipe data primary key adalah string

    protected $fillable = [
        'product_code',
        'product_name',
        'product_initial',
        'is_active',
    ];

    public static function generateProductCode()
    {
        $lastProduct = self::orderBy('product_code', 'desc')->first();
        $nextNumber = $lastProduct ? ((int) substr($lastProduct->product_code, 4)) + 1 : 1;
        return 'PRD-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_product_specification', 'product_code', 'brand_code', 'product_code', 'brand_code')
            ->withPivot('specification_id')
            ->withTimestamps();
    }

    public function specifications()
    {
        return $this->belongsToMany(Specification::class, 'brand_product_specification', 'product_code', 'specification_id', 'product_code', 'id')
            ->withPivot('brand_code')
            ->withTimestamps();
    }

}
