<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
    use HasFactory;

    protected $table = 'material_requests';
    protected $primaryKey = 'mr_code';
    protected $keyType = 'string';
    public $incrementing = false;

    public $timestamps = true;
    
    protected $fillable = ['mr_code', 'project_id', 'note', 'created_by', 'status'];

    const STATUS_CREATED = 'created';
    const STATUS_APPROVED = 'approved';
    const STATUS_PARTIAL = 'partial';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    public function updateStatus(string $newStatus)
    {
        $validStatuses = [
            self::STATUS_CREATED,
            self::STATUS_APPROVED,
            self::STATUS_PARTIAL,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];

        if (!in_array($newStatus, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid status: {$newStatus}");
        }

        $this->status = $newStatus;
        $this->save();
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function items()
    {
        return $this->hasMany(MaterialRequestItem::class, 'mr_code', 'mr_code');
    }

    public function requestor()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}
