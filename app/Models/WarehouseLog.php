<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseLog extends Model
{
    use HasFactory;

    protected $table = 'warehouse_logs';

    public $timestamps = true;

    protected $fillable = ['mr_item_id', 'fulfilled_quantity', 'remaining_quantity'];

    public function materialRequestItem()
    {
        return $this->belongsTo(MaterialRequestItem::class, 'mr_item_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}
