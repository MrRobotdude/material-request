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
