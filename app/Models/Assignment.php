<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    protected function assignment(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => image($value),
        );
    }

    public function answers(){
        return $this->hasMany(Answer::class);
    }

    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }

}
