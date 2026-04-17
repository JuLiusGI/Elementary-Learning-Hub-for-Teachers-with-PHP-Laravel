<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Attendance $attendance): bool
    {
        if ($user->isHeadTeacher()) {
            return true;
        }
        return $user->grade_level === $attendance->student->grade_level;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Attendance $attendance): bool
    {
        if ($user->isHeadTeacher()) {
            return true;
        }
        return $user->grade_level === $attendance->student->grade_level;
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->isHeadTeacher();
    }
}
