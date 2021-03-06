<?php

declare(strict_types=1);

namespace Milanowicz\Testing;

final class TestPerformanceTest extends TestCase
{
    public function dataCallbacks(): array
    {
        return [
            [
                static function () {
                    usleep(50);
                },
                static function () {
                    usleep(100);
                },
                [
                    'function1' => [
                        1.1, 1.2, 1.1, 1.2, 1.1, 1.2
                    ],
                    'function2' => [
                        1.2, 1.1, 1.2, 1.1, 1.2, 1.1
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider dataCallbacks
     */
    public function testClearTimes(
        callable $cb1,
        callable $cb2,
        array $timeMeasures
    ): void {
        $this->checkMeasures($cb1, $cb2, $timeMeasures);

        $this->clearTimes();
        $this->assertCount(0, $this->getTimeMeasures());
        $this->assertCount(0, $this->getTimeSignificance());
        $this->assertCount(0, $this->getTimeStats());
    }

    /**
     * @dataProvider dataCallbacks
     */
    public function testCheckMeanTimeOne(
        callable $cb1,
        callable $cb2,
        array $timeMeasures
    ): void {
        $this->tryTest(function () use ($cb1, $cb2, $timeMeasures) {
            $this->checkMeasures($cb1, $cb2, $timeMeasures);

            $this->checkMeanTime();
        });
    }

    public function testCheckMeanTimeOneException(): void
    {
        $this->timeMeasures = [
            'function1' => [4, 5, 4],
            'function2' => [1, 2, 3],
        ];
        $this->expectException(AssertionFailedException::class);
        $this->expectExceptionMessageMatches('/function1 > function2/');
        $this->expectExceptionMessageMatches('/Data:/');
        $this->checkMeanTime();
    }

    /**
     * @dataProvider dataCallbacks
     */
    public function testCheckMeanTimeTwo(
        callable $cb2,
        callable $cb1,
        array $timeMeasures
    ): void {
        $this->tryTest(function () use ($cb1, $cb2, $timeMeasures) {
            $this->checkMeasures($cb1, $cb2, $timeMeasures);

            $this->checkMeanTime(false);
        });
    }

    public function testCheckMeanTimeTwoException(): void
    {
        $this->timeMeasures = [
            'function1' => [1, 2, 3],
            'function2' => [3, 4, 5],
        ];
        $this->expectException(AssertionFailedException::class);
        $this->expectExceptionMessageMatches('/function1 < function2/');
        $this->expectExceptionMessageMatches('/Data:/');
        $this->checkMeanTime(false);
    }

    /**
     * @dataProvider dataCallbacks
     */
    public function testCheckPerformance(
        callable $cb1,
        callable $cb2,
        array $timeMeasures
    ): void {
        $this->tryTest(function () use ($cb1, $cb2, $timeMeasures) {
            $this->checkMeasures($cb1, $cb2, $timeMeasures);

            $this->checkPerformance();
        }, 10); // For MacOS Environment!
    }

    /**
     * @dataProvider dataCallbacks
     */
    public function testCheckStudentTest(
        callable $cb1,
        callable $cb2,
        array $timeMeasures
    ): void {
        $this->tryTest(function () use ($cb1, $cb2, $timeMeasures) {
            $this->checkMeasures($cb1, $cb2, $timeMeasures);

            $this->checkStudentTest();
            $this->assertCount(8, $this->getTimeSignificance());
        }, 10); // For MacOS Environment!
    }

    public function testCheckStudentTestValueException(): void
    {
        $this->timeSignificance = [
            'p1' => 0.0501
        ];

        $this->expectException(AssertionFailedException::class);
        $this->expectExceptionMessageMatches('/function1 > function2/');
        $this->expectExceptionMessageMatches('/Data:/');
        $this->checkStudentTest();
    }

    public function testCheckStudentTestValueLimes(): void
    {
        $this->timeSignificance = [
            'p1' => 0.05
        ];

        $this->checkStudentTest();
        $this->assertCount(1, $this->getTimeSignificance());
    }

    /**
     * @dataProvider dataCallbacks
     */
    public function testCheckStudentTestException(
        callable $cb1,
        callable $cb2,
        array $timeMeasures
    ): void {
        $this->checkMeasures($cb1, $cb2, $timeMeasures);
        $this->clearTimes();

        $this->timeMeasures = $timeMeasures;
        $this->expectException(AssertionFailedException::class);
        $this->expectExceptionMessageMatches('/p Value is bigger then expected/');
        $this->expectExceptionMessageMatches('/Data:/');

        $this->checkStudentTest();
    }

    private function checkMeasures(
        callable $cb1,
        callable $cb2,
        array $timeMeasures
    ): void {
        $this->timeMeasures = $timeMeasures;
        $this->measureTime($cb1, $cb2);

        $data = $this->getTimeMeasures();
        $this->assertCount(2, $data);
        $this->assertCount(20, $data['function1']);
        foreach ($data['function1'] as $value) {
            $this->assertIsNumeric($value);
        }
        $this->assertCount(20, $data['function2']);
        foreach ($data['function2'] as $value) {
            $this->assertIsNumeric($value);
        }

        $data = $this->getTimeSignificance();
        $this->assertCount(8, $data);
        $this->assertArrayHasKey('t', $data);
        $this->assertArrayHasKey('df', $data);
        $this->assertArrayHasKey('p1', $data);
        $this->assertArrayHasKey('p2', $data);
        $this->assertArrayHasKey('mean1', $data);
        $this->assertArrayHasKey('mean2', $data);
        $this->assertArrayHasKey('sd1', $data);
        $this->assertArrayHasKey('sd2', $data);

        $data = $this->getTimeStats();
        $this->assertArrayHasKey('function1', $data);
        $this->assertGreaterThan(0, $data['function1']['mean']);
        $this->assertLessThan(1, $data['function1']['mean']);
        $this->assertGreaterThan(0, $data['function1']['sd']);
        $this->assertLessThan(1, $data['function1']['sd']);
        $this->assertGreaterThan(0, $data['function1']['median']);
        $this->assertLessThan(1, $data['function1']['median']);
        $this->assertEquals(20, $data['function1']['n']);

        $this->assertArrayHasKey('function2', $data);
        $this->assertGreaterThan(0, $data['function2']['mean']);
        $this->assertLessThan(1, $data['function2']['mean']);
        $this->assertGreaterThan(0, $data['function2']['sd']);
        $this->assertLessThan(1, $data['function2']['sd']);
        $this->assertGreaterThan(0, $data['function2']['median']);
        $this->assertLessThan(1, $data['function2']['median']);
        $this->assertEquals(20, $data['function2']['n']);
    }
}
