<?php

namespace App\Policies;

use App\Models\LearningMaterial;
use App\Models\User;

class LearningMaterialPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, LearningMaterial $learningMaterial): bool
    {
        if ($user->isHeadTeacher()) {
            return true;
        }
        return $user->grade_level === $learningMaterial->grade_level;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, LearningMaterial $learningMaterial): bool
    {
        if ($user->isHeadTeacher()) {
            return true;
        }
        return $user->grade_level === $learningMaterial->grade_level && $learningMaterial->uploaded_by === $user->id;
    }

    public function delete(User $user, LearningMaterial $learningMaterial): bool
    {
        if ($user->isHeadTeacher()) {
            return true;
        }
        return $learningMaterial->uploaded_by === $user->id;
    }
}
