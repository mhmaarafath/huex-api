<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function grade(){
        return $this->belongsTo(Grade::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function subjects(){
        return $this->belongsToMany(Subject::class, 'student_subject');
    }
}
