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

    const STATUS_PENDING = 'pending';
    const STATUS_PARTIAL = 'partial';
    const STATUS_FULFILLED = 'fulfilled';

    public function updateStatus(string $newStatus)
    {
        $validStatuses = [
            self::STATUS_PENDING,
            self::STATUS_PARTIAL,
            self::STATUS_FULFILLED,
        ];

        if (!in_array($newStatus, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid status: {$newStatus}");
        }

        $this->status = $newStatus;
        $this->save();
    }

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
