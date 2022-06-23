<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonsGroupsStudent extends Model
{
    use HasFactory;

    protected $table = 'lessons_groups_student';

    public function group ()
    {
        return $this->belongsTo(LessonsGroups::class, 'lessons_groups_id');
    }

    protected $guarded = [];
}
