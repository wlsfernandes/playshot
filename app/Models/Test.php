<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;
    protected $fillable = ['resource_id'];

    public function resource()
    {
        return $this->morphOne(Resource::class, 'resourceable');
    }
    public function studentTest()
    {
        return $this->hasMany(StudentTest::class);
    }
}
