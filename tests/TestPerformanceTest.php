<?php

declare(strict_types=1);

namespace Milanowicz\Testing;

use PHPUnit\Framework\ExpectationFailedException;

final class TestPerformanceTest extends TestCase
{
    /**
     * @dataProvider dataCallbacks
     */
    public function testPerformanceMeasureOne(
        callable $cb1,
        callable $cb2
    ): void {
        $this->tryTest(function () use ($cb1, $cb2) {
            $this->measurePerformanceTime($cb1, $cb2);
            $this->assertCount(2, $this->getPerformanceMeasures());
            $this->assertCount(20, $this->getPerformanceMeasures()['func1']);
            $this->assertCount(20, $this->getPerformanceMeasures()['func2']);

            $data = $this->getPerformanceStats();
            $this->assertArrayHasKey('func1', $data);
            $this->assertGreaterThan(0, $data['func1']['mean']);
            $this->assertLessThan(1, $data['func1']['mean']);
            $this->assertGreaterThan(0, $data['func1']['sd']);
            $this->assertLessThan(1, $data['func1']['sd']);
            $this->assertGreaterThan(0, $data['func1']['median']);
            $this->assertLessThan(1, $data['func1']['median']);
            $this->assertEquals(20, $data['func1']['n']);

            $this->assertArrayHasKey('func2', $data);
            $this->assertGreaterThan(0, $data['func2']['mean']);
            $this->assertLessThan(1, $data['func2']['mean']);
            $this->assertGreaterThan(0, $data['func2']['sd']);
            $this->assertLessThan(1, $data['func2']['sd']);
            $this->assertGreaterThan(0, $data['func2']['median']);
            $this->assertLessThan(1, $data['func2']['median']);
            $this->assertEquals(20, $data['func2']['n']);

            $this->checkPerformanceTime();

            try {
                $this->checkPerformanceTime(false);
            } catch (ExpectationFailedException $exception) {
                $this->assertInstanceOf(ExpectationFailedException::class, $exception);
                $this->assertStringContainsString('func1 < func2', $exception->getMessage());
            }
        });
    }

    /**
     * @dataProvider dataCallbacks
     */
    public function testPerformanceMeasureTwo(
        callable $cb2,
        callable $cb1
    ): void {
        $this->tryTest(function () use ($cb1, $cb2) {
            $this->measurePerformanceTime($cb1, $cb2);
            $this->assertCount(2, $this->getPerformanceMeasures());
            $this->assertCount(20, $this->getPerformanceMeasures()['func1']);
            $this->assertCount(20, $this->getPerformanceMeasures()['func2']);

            $data = $this->getPerformanceStats();
            $this->assertArrayHasKey('func1', $data);
            $this->assertGreaterThan(0, $data['func1']['mean']);
            $this->assertLessThan(1, $data['func1']['mean']);
            $this->assertGreaterThan(0, $data['func1']['sd']);
            $this->assertLessThan(1, $data['func1']['sd']);
            $this->assertGreaterThan(0, $data['func1']['median']);
            $this->assertLessThan(1, $data['func1']['median']);
            $this->assertEquals(20, $data['func1']['n']);

            $this->assertArrayHasKey('func2', $data);
            $this->assertGreaterThan(0, $data['func2']['mean']);
            $this->assertLessThan(1, $data['func2']['mean']);
            $this->assertGreaterThan(0, $data['func2']['sd']);
            $this->assertLessThan(1, $data['func2']['sd']);
            $this->assertGreaterThan(0, $data['func2']['median']);
            $this->assertLessThan(1, $data['func2']['median']);
            $this->assertEquals(20, $data['func2']['n']);

            $this->checkPerformanceTime(false);

            try {
                $this->checkPerformanceTime();
            } catch (ExpectationFailedException $exception) {
                $this->assertInstanceOf(ExpectationFailedException::class, $exception);
                $this->assertStringContainsString('func1 > func2', $exception->getMessage());
            }
        });
    }

    /**
     * @dataProvider dataCallbacks
     */
    public function testCheckPerformanceAll(
        callable $cb1,
        callable $cb2
    ): void {
        $this->tryTest(function () use ($cb1, $cb2) {
            $this->measurePerformanceTime($cb1, $cb2);
            $this->assertCount(2, $this->getPerformanceMeasures());

            $this->checkPerformanceAll();
        });
    }

    /**
     * @dataProvider dataCallbacks
     */
    public function testStudentTest(
        callable $cb1,
        callable $cb2
    ): void {
        $this->tryTest(function () use ($cb1, $cb2) {
            $this->measurePerformanceTime($cb1, $cb2);
            $this->assertCount(2, $this->getPerformanceMeasures());

            $this->checkStudentTest();
        });
    }

    public function dataCallbacks(): array
    {
        return [
            [
                static function () {
                    usleep(90);
                },
                static function () {
                    usleep(100);
                }
            ]
        ];
    }

    public function testStudentTestException(): void
    {
        $this->timeMeasures = [
            'func1' => [
                1.1, 1.2, 1.1, 1.2, 1.1, 1.2
            ],
            'func2' => [
                1.2, 1.1, 1.2, 1.1, 1.2, 1.1
            ]
        ];

        $this->expectException(ExceptionAssertionFailed::class);
        $this->expectExceptionMessageMatches('/p Value is bigger then expected/');
        $this->expectExceptionMessageMatches('/Data:/');

        $this->checkStudentTest();
    }
}
