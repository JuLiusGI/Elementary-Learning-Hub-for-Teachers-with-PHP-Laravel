<?php

namespace Database\Seeders;

use App\Models\GradeLevelSubject;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        // Create all subjects
        $subjects = [
            // Kindergarten domains
            ['name' => 'Socio-Emotional Development', 'code' => 'SOCIO', 'display_order' => 1],
            ['name' => 'Language Development', 'code' => 'LANG_DEV', 'display_order' => 2],
            ['name' => 'Cognitive Development', 'code' => 'COGNITIVE', 'display_order' => 3],
            ['name' => 'Physical Development', 'code' => 'PHYSICAL', 'display_order' => 4],
            ['name' => 'Creative Expression', 'code' => 'CREATIVE', 'display_order' => 5],
            // Grades 1-3 subjects
            ['name' => 'Reading & Literacy', 'code' => 'READING', 'display_order' => 10],
            ['name' => 'Language', 'code' => 'LANGUAGE', 'display_order' => 11],
            ['name' => 'Makabansa', 'code' => 'MAKABANSA', 'display_order' => 12],
            ['name' => 'GMRC / EsP', 'code' => 'GMRC', 'display_order' => 13],
            ['name' => 'Mathematics', 'code' => 'MATH', 'display_order' => 14],
            // Grades 2-6 subjects
            ['name' => 'Filipino', 'code' => 'FIL', 'display_order' => 20],
            ['name' => 'English', 'code' => 'ENG', 'display_order' => 21],
            // Grades 3-6 subjects
            ['name' => 'Science', 'code' => 'SCI', 'display_order' => 30],
            // Grades 4-6 subjects
            ['name' => 'Araling Panlipunan', 'code' => 'AP', 'display_order' => 40],
            ['name' => 'EPP', 'code' => 'EPP', 'display_order' => 41],
            ['name' => 'MAPEH', 'code' => 'MAPEH', 'display_order' => 42],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }

        // Map subjects to grade levels
        $gradeLevelMappings = [
            'kinder' => ['SOCIO', 'LANG_DEV', 'COGNITIVE', 'PHYSICAL', 'CREATIVE'],
            'grade_1' => ['READING', 'LANGUAGE', 'MAKABANSA', 'GMRC', 'MATH'],
            'grade_2' => ['MAKABANSA', 'GMRC', 'MATH', 'FIL', 'ENG'],
            'grade_3' => ['MAKABANSA', 'GMRC', 'MATH', 'FIL', 'ENG', 'SCI'],
            'grade_4' => ['GMRC', 'MATH', 'FIL', 'ENG', 'SCI', 'AP', 'EPP', 'MAPEH'],
            'grade_5' => ['GMRC', 'MATH', 'FIL', 'ENG', 'SCI', 'AP', 'EPP', 'MAPEH'],
            'grade_6' => ['GMRC', 'MATH', 'FIL', 'ENG', 'SCI', 'AP', 'EPP', 'MAPEH'],
        ];

        foreach ($gradeLevelMappings as $gradeLevel => $codes) {
            $subjectIds = Subject::whereIn('code', $codes)->pluck('id', 'code');

            foreach ($codes as $order => $code) {
                GradeLevelSubject::create([
                    'grade_level' => $gradeLevel,
                    'subject_id' => $subjectIds[$code],
                    'display_order' => $order + 1,
                ]);
            }
        }
    }
}
