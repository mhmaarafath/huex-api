<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected function answer(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => image($value),
        );
    }

    public function assignment(){
        return $this->belongsTo(Assignment::class);
    }

    public function student(){
        return $this->belongsTo(Student::class);
    }

}
