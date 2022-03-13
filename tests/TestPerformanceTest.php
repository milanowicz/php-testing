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
    public function testPerformanceMeasureOne(
        callable $cb1,
        callable $cb2,
        array $timeMeasures
    ): void {
        $this->tryTest(function () use ($cb1, $cb2, $timeMeasures) {
            $this->checkMeasures($cb1, $cb2, $timeMeasures);

            $this->checkMeanTime();
        });
    }

    /**
     * @dataProvider dataCallbacks
     */
    public function testPerformanceMeasureOneException(
        callable $cb1,
        callable $cb2,
        array $timeMeasures
    ): void {
        $this->tryTest(function () use ($cb1, $cb2, $timeMeasures) {
            $this->checkMeasures($cb1, $cb2, $timeMeasures);

            $this->expectException(AssertionFailedException::class);
            $this->expectExceptionMessageMatches('/function1 < function2/');
            $this->expectExceptionMessageMatches('/Data:/');
            $this->checkMeanTime(false);
        });
    }

    /**
     * @dataProvider dataCallbacks
     */
    public function testPerformanceMeasureTwo(
        callable $cb2,
        callable $cb1,
        array $timeMeasures
    ): void {
        $this->tryTest(function () use ($cb1, $cb2, $timeMeasures) {
            $this->checkMeasures($cb1, $cb2, $timeMeasures);

            $this->checkMeanTime(false);
        });
    }

    /**
     * @dataProvider dataCallbacks
     */
    public function testPerformanceMeasureTwoException(
        callable $cb2,
        callable $cb1,
        array $timeMeasures
    ): void {
        $this->tryTest(function () use ($cb1, $cb2, $timeMeasures) {
            $this->checkMeasures($cb1, $cb2, $timeMeasures);

            $this->expectException(AssertionFailedException::class);
            $this->expectExceptionMessageMatches('/function1 > function2/');
            $this->expectExceptionMessageMatches('/Data:/');
            $this->checkMeanTime();
        });
    }

    /**
     * @dataProvider dataCallbacks
     */
    public function testCheckPerformanceAll(
        callable $cb1,
        callable $cb2,
        array $timeMeasures
    ): void {
        $this->tryTest(function () use ($cb1, $cb2, $timeMeasures) {
            $this->checkMeasures($cb1, $cb2, $timeMeasures);

            $this->checkPerformance();
        }, 10);
    }

    /**
     * @dataProvider dataCallbacks
     */
    public function testStudentTest(
        callable $cb1,
        callable $cb2,
        array $timeMeasures
    ): void {
        $this->tryTest(function () use ($cb1, $cb2, $timeMeasures) {
            $this->checkMeasures($cb1, $cb2, $timeMeasures);

            $this->checkStudentTest();
            $this->assertCount(8, $this->getTimeSignificance());
        });
    }

    public function testStudentTestValueException(): void
    {
        $this->timeSignificance = [
            'p1' => 0.0501
        ];

        $this->expectException(AssertionFailedException::class);
        $this->expectExceptionMessageMatches('/function1 > function2/');
        $this->expectExceptionMessageMatches('/Data:/');
        $this->checkStudentTest();
    }

    public function testStudentTestValueLimes(): void
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
    public function testStudentTestException(
        callable $cb1,
        callable $cb2,
        array $timeMeasures
    ): void {
        $this->checkMeasures($cb1, $cb2, $timeMeasures);

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

        $this->assertCount(0, $this->getTimeSignificance());
        $this->assertCount(2, $this->getTimeMeasures());
        $this->assertCount(20, $this->getTimeMeasures()['function1']);
        $this->assertCount(20, $this->getTimeMeasures()['function2']);

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

    /**
     * @dataProvider dataCallbacks
     */
    public function testResetTimes(
        callable $cb1,
        callable $cb2,
        array $timeMeasures
    ): void {
        $this->checkMeasures($cb1, $cb2, $timeMeasures);
        $this->clearTimes();
        $this->assertCount(0, $this->getTimeMeasures());
        $this->assertCount(0, $this->getTimeSignificance());
    }
}
