<?php

namespace App\Services;

class GradeCalculatorService
{
    public function calculate(
        ?float $wwTotal,
        ?float $wwMax,
        ?float $ptTotal,
        ?float $ptMax,
        ?float $qaScore,
        ?float $qaMax
    ): array {
        $wwWeight = config('school.grading.ww_weight');
        $ptWeight = config('school.grading.pt_weight');
        $qaWeight = config('school.grading.qa_weight');
        $passingGrade = config('school.grading.passing_grade');

        $wwPercentage = ($wwMax > 0) ? ($wwTotal / $wwMax) * 100 : 0;
        $wwWeighted = $wwPercentage * $wwWeight;

        $ptPercentage = ($ptMax > 0) ? ($ptTotal / $ptMax) * 100 : 0;
        $ptWeighted = $ptPercentage * $ptWeight;

        $qaPercentage = ($qaMax > 0) ? ($qaScore / $qaMax) * 100 : 0;
        $qaWeighted = $qaPercentage * $qaWeight;

        $quarterlyGrade = round($wwWeighted + $ptWeighted + $qaWeighted, 2);
        $remarks = $quarterlyGrade >= $passingGrade ? 'Passed' : 'Failed';

        return [
            'ww_percentage' => round($wwPercentage, 2),
            'ww_weighted' => round($wwWeighted, 2),
            'pt_percentage' => round($ptPercentage, 2),
            'pt_weighted' => round($ptWeighted, 2),
            'qa_percentage' => round($qaPercentage, 2),
            'qa_weighted' => round($qaWeighted, 2),
            'quarterly_grade' => $quarterlyGrade,
            'remarks' => $remarks,
        ];
    }

    public function calculateFinalGrade(array $quarterlyGrades): ?float
    {
        if (count($quarterlyGrades) !== 4) {
            return null;
        }
        return round(array_sum($quarterlyGrades) / 4, 2);
    }

    public function getFinalRemarks(float $finalGrade): string
    {
        return $finalGrade >= config('school.grading.passing_grade') ? 'Passed' : 'Failed';
    }
}
