<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = ['resource_id'];

    public function resource()
    {
        return $this->morphOne(Resource::class, 'resourceable');
    }

    public function studentTasks()
    {
        return $this->hasMany(StudentTask::class);
    }
}
