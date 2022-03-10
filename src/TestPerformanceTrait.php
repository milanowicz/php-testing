<?php

declare(strict_types=1);

namespace Milanowicz\Testing;

use MathPHP\Statistics\{Average, Descriptive, Significance};

trait TestPerformanceTrait
{
    protected array $timeMeasures = [];
    protected array $timeStats = [];

    final protected function measureTime(
        callable $function1,
        callable $function2,
        int $n = 20
    ): void {
        $this->resetTimes();
        for ($i = 0; $i < $n; $i++) {
            $start = microtime(true);
            $function1();
            $end = microtime(true);
            $this->timeMeasures['function1'][] = $end - $start;
        }

        for ($i = 0; $i < $n; $i++) {
            $start = microtime(true);
            $function2();
            $end = microtime(true);
            $this->timeMeasures['function2'][] = $end - $start;
        }
    }

    /**
     * @throws AssertionFailedException
     * @throws \MathPHP\Exception\BadDataException
     * @throws \MathPHP\Exception\OutOfBoundsException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @infection-ignore-all
     */
    final protected function checkPerformance(
        bool $function1 = true,
        float $pValue = 0.05
    ): void {
        $this->checkMeanTime($function1);
        $this->checkStudentTest($pValue);
    }

    /**
     * @throws \MathPHP\Exception\BadDataException
     * @throws \MathPHP\Exception\OutOfBoundsException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @infection-ignore-all
     */
    final protected function checkMeanTime(
        bool $function1 = true
    ): void {
        if ($function1) {
            $this->assertGreaterThan(
                $this->getTimeStats()['function1']['mean'],
                $this->getTimeStats()['function2']['mean'],
                'function1 > function2'
            );
        } else {
            $this->assertLessThan(
                $this->getTimeStats()['function1']['mean'],
                $this->getTimeStats()['function2']['mean'],
                'function1 < function2'
            );
        }
    }

    /**
     * @throws AssertionFailedException
     * @throws \MathPHP\Exception\OutOfBoundsException
     */
    final protected function checkStudentTest(
        float $pValue = 0.05
    ): void {
        $data = Significance::tTestTwoSample(
            $this->timeMeasures['function1'],
            $this->timeMeasures['function2'],
        );

        // @infection-ignore-all
        if ($data['p1'] >= $pValue) {
            throw new AssertionFailedException(
                'p Value is bigger then expected',
                $data
            );
        }
    }

    final protected function getTimeMeasures(): array
    {
        return $this->timeMeasures;
    }

    /**
     * @throws \MathPHP\Exception\BadDataException
     * @throws \MathPHP\Exception\OutOfBoundsException
     */
    final protected function getTimeStats(): array
    {
        if (count($this->timeStats) === 0) {
            $this->timeStats = [
                'function1' => [
                    'mean' => Average::mean($this->timeMeasures['function1']),
                    'median' => Average::median($this->timeMeasures['function1']),
                    'sd' => Descriptive::standardDeviation($this->timeMeasures['function1']),
                    'n' => count($this->timeMeasures['function1']),
                ],
                'function2' => [
                    'mean' => Average::mean($this->timeMeasures['function2']),
                    'median' => Average::median($this->timeMeasures['function2']),
                    'sd' => Descriptive::standardDeviation($this->timeMeasures['function2']),
                    'n' => count($this->timeMeasures['function2']),
                ]
            ];
        }
        return $this->timeStats;
    }

    final protected function resetTimes(): self
    {
        $this->timeMeasures = [];
        $this->timeStats = [];
        return $this;
    }
}
