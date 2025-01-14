<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specification extends Model
{
    use HasFactory;

    protected $table = 'specifications';

    protected $primaryKey = 'specification_id';

    protected $fillable = [
        'specification_name',
        'unit',
        'is_active',
    ];

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_product_specification', 'specification_id', 'brand_code', 'id', 'brand_code')
            ->withPivot('product_code')
            ->withTimestamps();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'brand_product_specification', 'specification_id', 'product_code', 'id', 'product_code')
            ->withPivot('brand_code')
            ->withTimestamps();
    }

}
