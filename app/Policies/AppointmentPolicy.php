<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AppointmentPolicy
{
    public function view(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->patient_id;
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->patient_id;
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->patient_id;
    }

    public function viewDoctor(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->patient_id;
    }

    public function accept(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->doctor_id;
    }

    public function reject(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->doctor_id;
    }
}
