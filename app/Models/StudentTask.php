<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentTask extends Model
{
    use HasFactory;
    protected $fillable = ['student_id', 'task_id', 'url', 'answer', 'created_at', 'update_at'];
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id'); // Assuming students are stored in the `users` table
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
