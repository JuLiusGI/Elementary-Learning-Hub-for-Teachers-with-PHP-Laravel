<?php

namespace App\Policies;

use App\Models\KinderAssessment;
use App\Models\User;

class KinderAssessmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isHeadTeacher() || $user->grade_level === 'kinder';
    }

    public function view(User $user, KinderAssessment $assessment): bool
    {
        if ($user->isHeadTeacher()) {
            return true;
        }
        return $user->grade_level === 'kinder';
    }

    public function create(User $user): bool
    {
        return $user->isHeadTeacher() || $user->grade_level === 'kinder';
    }

    public function update(User $user, KinderAssessment $assessment): bool
    {
        if ($user->isHeadTeacher()) {
            return true;
        }
        return $user->grade_level === 'kinder' && $assessment->isDraft();
    }

    public function delete(User $user, KinderAssessment $assessment): bool
    {
        return $user->isHeadTeacher() && $assessment->isDraft();
    }

    public function submit(User $user): bool
    {
        return $user->isHeadTeacher() || $user->grade_level === 'kinder';
    }

    public function approve(User $user): bool
    {
        return $user->isHeadTeacher();
    }
}
