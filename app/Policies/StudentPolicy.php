<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Student $student): bool
    {
        if ($user->isHeadTeacher()) {
            return true;
        }
        return $user->grade_level === $student->grade_level;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Student $student): bool
    {
        if ($user->isHeadTeacher()) {
            return true;
        }
        return $user->grade_level === $student->grade_level;
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->isHeadTeacher();
    }
}
