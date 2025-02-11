<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequestItem extends Model
{
    use HasFactory;

    protected $table = 'material_request_items';

    public $timestamps = true;
    protected $fillable = ['mr_code', 'item_id', 'quantity', 'fulfilled_quantity', 'status'];

    public function materialRequest()
    {
        return $this->belongsTo(MaterialRequest::class, 'mr_code', 'mr_code');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'item_id');
    }

    public function warehouseLogs()
    {
        return $this->hasMany(WarehouseLog::class, 'mr_item_id', 'id');
    }
}
