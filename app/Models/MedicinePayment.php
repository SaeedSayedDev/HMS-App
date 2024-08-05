<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicinePayment extends Model
{
    use HasFactory;

    protected $fillable = ['payment_id', 'patient_id', 'medicine_id', 'quantity', 'amount'];

    public function patient()
    {
        return $this->belongsTo(User::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
