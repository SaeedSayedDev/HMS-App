<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminPermission extends Model
{
    use HasFactory;
    protected $fillable = [
        'admin_id',
        'admin_management',
        'doctor_management',
        'salary_management',
        'absence_management',
        'medicine_management',
    ];
}
