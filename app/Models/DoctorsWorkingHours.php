<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorsWorkingHours extends Model
{
    use HasFactory;
    protected $fillable = [
        'doctor_id',
        'day_name',
        'start_time',
        'end_time',
    ];
}
