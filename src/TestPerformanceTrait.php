<?php

declare(strict_types=1);

namespace Milanowicz\Testing;

use MathPHP\Statistics\{Average, Descriptive, Significance};
use PHPUnit\Framework\ExpectationFailedException;

trait TestPerformanceTrait
{
    protected array $timeMeasures = [];
    protected array $timeSignificance = [];
    protected array $timeStats = [];

    protected function clearTimes(): self
    {
        $this->timeMeasures = [];
        $this->timeSignificance = [];
        $this->timeStats = [];
        return $this;
    }

    protected function measureTime(
        callable $function1,
        callable $function2,
        int $n = 20
    ): self {
        $this->clearTimes();
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
        return $this;
    }

    /**
     * @throws AssertionFailedException
     * @throws \MathPHP\Exception\BadDataException
     * @throws \MathPHP\Exception\OutOfBoundsException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    protected function checkPerformance(
        bool $function1 = true,
        float $pValue = 0.05
    ): self {
        return $this
            ->checkMeanTime($function1)
            ->checkStudentTest($pValue);
    }

    /**
     * @throws \MathPHP\Exception\BadDataException
     * @throws \MathPHP\Exception\OutOfBoundsException
     * @throws AssertionFailedException
     */
    protected function checkMeanTime(
        bool $function1 = true
    ): self {
        try {
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
        } catch (ExpectationFailedException $exception) {
            throw new AssertionFailedException(
                $exception->getMessage(),
                $this->getTimeStats(),
                $exception->getCode(),
                $exception
            );
        }
        return $this;
    }

    /**
     * @throws AssertionFailedException
     * @throws \MathPHP\Exception\OutOfBoundsException
     */
    protected function checkStudentTest(
        float $pValue = 0.05
    ): self {
        if ($this->getTimeSignificance()['p1'] > $pValue) {
            throw new AssertionFailedException(
                'p Value is bigger then expected',
                $this->getTimeSignificance()
            );
        }
        return $this;
    }

    protected function getTimeMeasures(): array
    {
        return $this->timeMeasures;
    }

    /**
     * @throws \MathPHP\Exception\OutOfBoundsException
     * @return array [
     *   t     => t score
     *   df    => degrees of freedom
     *   p1    => one-tailed p value
     *   p2    => two-tailed p value
     *   mean1 => mean of sample set 1
     *   mean2 => mean of sample set 2
     *   sd1   => standard deviation of sample set 1
     *   sd2   => standard deviation of sample set 2
     * ]
     */
    protected function getTimeSignificance(): array
    {
        if (
            isset($this->timeMeasures['function1'], $this->timeMeasures['function2'])
            && count($this->timeSignificance) === 0
        ) {
            $this->timeSignificance = Significance::tTestTwoSample(
                $this->timeMeasures['function1'],
                $this->timeMeasures['function2'],
            );
        }
        return $this->timeSignificance;
    }

    /**
     * @throws \MathPHP\Exception\BadDataException
     * @throws \MathPHP\Exception\OutOfBoundsException
     */
    protected function getTimeStats(): array
    {
        if (
            isset($this->timeMeasures['function1'], $this->timeMeasures['function2'])
            && count($this->timeStats) === 0
        ) {
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
}
