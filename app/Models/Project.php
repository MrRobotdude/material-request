<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';
    protected $primaryKey = 'project_id';
    protected $fillable = ['project_name', 'description'];

    public function materialRequests()
    {
        return $this->hasMany(MaterialRequest::class, 'project_id', 'project_id');
    }
}
