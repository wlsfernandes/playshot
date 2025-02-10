<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentTest extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'test_id',
        'url',
        'answer',
        'start_time',
        'submitted_at',
        'submitted_within_time',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'start_time' => 'datetime',
        'submitted_at' => 'datetime',
    ];
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id'); // Assuming students are stored in the `users` table
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }
}
