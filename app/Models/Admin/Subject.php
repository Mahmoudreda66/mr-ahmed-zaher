<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
