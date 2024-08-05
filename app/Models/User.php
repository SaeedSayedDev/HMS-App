<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'birth_date',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function otp()
    {
        return $this->hasOne(Otp::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function admin_premission()
    {
        return $this->hasOne(AdminPermission::class, 'admin_id',  'id');
    }

    public function workingHours()
    {
        return $this->hasMany(DoctorsWorkingHours::class, 'doctor_id');
    }

    public function patientAppointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    public function doctorAppointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id')->with('patient');
    }

    public function medicinePayments()
    {
        return $this->hasMany(MedicinePayment::class, 'patient_id');
    }
}
