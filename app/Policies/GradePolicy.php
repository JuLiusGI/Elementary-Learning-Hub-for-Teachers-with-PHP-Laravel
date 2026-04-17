<?php

namespace App\Policies;

use App\Models\Grade;
use App\Models\User;

class GradePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Grade $grade): bool
    {
        if ($user->isHeadTeacher()) {
            return true;
        }
        return $user->grade_level === $grade->student->grade_level;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Grade $grade): bool
    {
        if ($user->isHeadTeacher()) {
            return true;
        }
        return $user->grade_level === $grade->student->grade_level && $grade->isDraft();
    }

    public function delete(User $user, Grade $grade): bool
    {
        return $user->isHeadTeacher() && $grade->isDraft();
    }

    public function submit(User $user): bool
    {
        return true;
    }

    public function approve(User $user): bool
    {
        return $user->isHeadTeacher();
    }
}
