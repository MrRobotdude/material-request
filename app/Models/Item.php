<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';
    protected $primaryKey = 'item_id';
    protected $fillable = [
        'brand_code',
        'product_code',
        'type_code',
        'sub_type_code',
        'item_code',
        'description',
        'unit',
        'is_active',
    ];

    public static function generateItemCode($typeInitial, $subTypeInitial)
    {
        $latest = self::latest('item_id')->first();
        $number = $latest ? $latest->item_id + 1 : 1;
        return strtoupper($typeInitial) . '-' . strtoupper($subTypeInitial) . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // Define relationships
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_code', 'brand_code');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_code', 'product_code');
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_code', 'type_code');
    }

    public function subType()
    {
        return $this->belongsTo(SubType::class, 'sub_type_code', 'sub_type_code');
    }

    public function materialRequestItems()
    {
        return $this->hasMany(MaterialRequestItem::class, 'item_id', 'item_id');
    }

    public function warehouseLogs()
    {
        return $this->hasMany(WarehouseLog::class, 'item_id', 'item_id');
    }
}