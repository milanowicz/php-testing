<?php

declare(strict_types=1);

namespace Milanowicz\Testing;

use MathPHP\Statistics\Average;
use MathPHP\Statistics\Descriptive;
use MathPHP\Statistics\Significance;

trait TestPerformanceTrait
{
    protected array $timeMeasures = [];

    final protected function measurePerformanceTime(
        callable $func1,
        callable $func2,
        int $n = 20
    ): void {
        $this->timeMeasures = [];
        for ($i = 0; $i < $n; $i++) {
            $start = microtime(true);
            $func1();
            $end = microtime(true);
            $this->timeMeasures['func1'][] = $end - $start;
        }

        for ($i = 0; $i < $n; $i++) {
            $start = microtime(true);
            $func2();
            $end = microtime(true);
            $this->timeMeasures['func2'][] = $end - $start;
        }
    }

    /**
     * @infection-ignore-all
     */
    final protected function checkPerformanceAll(
        bool $func1 = true,
        float $pValue = 0.05
    ): void {
        $this->checkPerformanceTime($func1);
        $this->checkStudentTest($pValue);
    }

    /**
     * @infection-ignore-all
     */
    final protected function checkPerformanceTime(
        bool $func1 = true
    ): void {
        if ($func1) {
            $this->assertGreaterThan(
                $this->getPerformanceStats()['func1']['mean'],
                $this->getPerformanceStats()['func2']['mean'],
                'func1 > func2'
            );
        } else {
            $this->assertLessThan(
                $this->getPerformanceStats()['func1']['mean'],
                $this->getPerformanceStats()['func2']['mean'],
                'func1 < func2'
            );
        }
    }

    final protected function checkStudentTest(
        float $pValue = 0.05
    ): void {
        $data = Significance::tTestTwoSample(
            $this->timeMeasures['func1'],
            $this->timeMeasures['func2'],
        );

        // @infection-ignore-all
        if ($data['p1'] >= $pValue) {
            throw new ExceptionAssertionFailed(
                'p Value is bigger then expected',
                $data
            );
        }
    }

    final protected function getPerformanceMeasures(): array
    {
        return $this->timeMeasures;
    }

    final protected function getPerformanceStats(): array
    {
        return [
            'func1' => [
                'mean' => Average::mean($this->timeMeasures['func1']),
                'median' => Average::median($this->timeMeasures['func1']),
                'sd' => Descriptive::standardDeviation($this->timeMeasures['func1']),
                'n' => count($this->timeMeasures['func1']),
            ],
            'func2' => [
                'mean' => Average::mean($this->timeMeasures['func2']),
                'median' => Average::median($this->timeMeasures['func2']),
                'sd' => Descriptive::standardDeviation($this->timeMeasures['func2']),
                'n' => count($this->timeMeasures['func2']),
            ]
        ];
    }
}
