<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
   protected $fillable = [
    'nik', 'full_name', 'phone', 'department_id', 
    'position', 'join_date', 'photo', 'is_active'
];
}
