<?php

namespace Tests\Unit\Services;

use App\Services\GradeCalculatorService;
use Tests\TestCase;

class GradeCalculatorServiceTest extends TestCase
{
    private GradeCalculatorService $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new GradeCalculatorService();
    }

    public function test_perfect_scores_yield_100(): void
    {
        $result = $this->calculator->calculate(100, 100, 100, 100, 100, 100);

        $this->assertEquals(100.00, $result['ww_percentage']);
        $this->assertEquals(40.00, $result['ww_weighted']);
        $this->assertEquals(100.00, $result['pt_percentage']);
        $this->assertEquals(40.00, $result['pt_weighted']);
        $this->assertEquals(100.00, $result['qa_percentage']);
        $this->assertEquals(20.00, $result['qa_weighted']);
        $this->assertEquals(100.00, $result['quarterly_grade']);
        $this->assertEquals('Passed', $result['remarks']);
    }

    public function test_zero_scores_yield_zero_and_failed(): void
    {
        $result = $this->calculator->calculate(0, 100, 0, 100, 0, 100);

        $this->assertEquals(0.00, $result['quarterly_grade']);
        $this->assertEquals('Failed', $result['remarks']);
    }

    public function test_boundary_exactly_75_is_passed(): void
    {
        // WW: 75/100=75% * 0.40 = 30
        // PT: 75/100=75% * 0.40 = 30
        // QA: 75/100=75% * 0.20 = 15
        // Total = 75.00
        $result = $this->calculator->calculate(75, 100, 75, 100, 75, 100);

        $this->assertEquals(75.00, $result['quarterly_grade']);
        $this->assertEquals('Passed', $result['remarks']);
    }

    public function test_below_75_is_failed(): void
    {
        // WW: 74/100=74% * 0.40 = 29.60
        // PT: 74/100=74% * 0.40 = 29.60
        // QA: 74/100=74% * 0.20 = 14.80
        // Total = 74.00
        $result = $this->calculator->calculate(74, 100, 74, 100, 74, 100);

        $this->assertEquals(74.00, $result['quarterly_grade']);
        $this->assertEquals('Failed', $result['remarks']);
    }

    public function test_partial_scores_calculated_correctly(): void
    {
        // WW: 80/100=80% * 0.40 = 32.00
        // PT: 70/100=70% * 0.40 = 28.00
        // QA: 90/100=90% * 0.20 = 18.00
        // Total = 78.00
        $result = $this->calculator->calculate(80, 100, 70, 100, 90, 100);

        $this->assertEquals(80.00, $result['ww_percentage']);
        $this->assertEquals(32.00, $result['ww_weighted']);
        $this->assertEquals(70.00, $result['pt_percentage']);
        $this->assertEquals(28.00, $result['pt_weighted']);
        $this->assertEquals(90.00, $result['qa_percentage']);
        $this->assertEquals(18.00, $result['qa_weighted']);
        $this->assertEquals(78.00, $result['quarterly_grade']);
        $this->assertEquals('Passed', $result['remarks']);
    }

    public function test_zero_max_score_prevents_division_by_zero(): void
    {
        $result = $this->calculator->calculate(50, 0, 50, 0, 50, 0);

        $this->assertEquals(0.00, $result['ww_percentage']);
        $this->assertEquals(0.00, $result['pt_percentage']);
        $this->assertEquals(0.00, $result['qa_percentage']);
        $this->assertEquals(0.00, $result['quarterly_grade']);
    }

    public function test_null_scores_treated_as_zero(): void
    {
        $result = $this->calculator->calculate(null, null, null, null, null, null);

        $this->assertEquals(0.00, $result['quarterly_grade']);
        $this->assertEquals('Failed', $result['remarks']);
    }

    public function test_different_max_scores(): void
    {
        // WW: 40/50 = 80% * 0.40 = 32.00
        // PT: 30/40 = 75% * 0.40 = 30.00
        // QA: 18/20 = 90% * 0.20 = 18.00
        // Total = 80.00
        $result = $this->calculator->calculate(40, 50, 30, 40, 18, 20);

        $this->assertEquals(80.00, $result['ww_percentage']);
        $this->assertEquals(32.00, $result['ww_weighted']);
        $this->assertEquals(75.00, $result['pt_percentage']);
        $this->assertEquals(30.00, $result['pt_weighted']);
        $this->assertEquals(90.00, $result['qa_percentage']);
        $this->assertEquals(18.00, $result['qa_weighted']);
        $this->assertEquals(80.00, $result['quarterly_grade']);
    }

    public function test_calculate_final_grade_with_four_quarters(): void
    {
        $finalGrade = $this->calculator->calculateFinalGrade([85, 90, 80, 85]);

        $this->assertEquals(85.00, $finalGrade);
    }

    public function test_calculate_final_grade_returns_null_with_fewer_than_four(): void
    {
        $this->assertNull($this->calculator->calculateFinalGrade([85, 90, 80]));
        $this->assertNull($this->calculator->calculateFinalGrade([85]));
        $this->assertNull($this->calculator->calculateFinalGrade([]));
    }

    public function test_calculate_final_grade_returns_null_with_more_than_four(): void
    {
        $this->assertNull($this->calculator->calculateFinalGrade([85, 90, 80, 85, 75]));
    }

    public function test_get_final_remarks_passed(): void
    {
        $this->assertEquals('Passed', $this->calculator->getFinalRemarks(75.00));
        $this->assertEquals('Passed', $this->calculator->getFinalRemarks(100.00));
        $this->assertEquals('Passed', $this->calculator->getFinalRemarks(85.50));
    }

    public function test_get_final_remarks_failed(): void
    {
        $this->assertEquals('Failed', $this->calculator->getFinalRemarks(74.99));
        $this->assertEquals('Failed', $this->calculator->getFinalRemarks(0.00));
        $this->assertEquals('Failed', $this->calculator->getFinalRemarks(50.00));
    }
}
