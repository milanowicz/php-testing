<?php

declare(strict_types=1);

namespace Milanowicz\Testing;

use Error;
use InvalidArgumentException;
use RuntimeException;
use stdClass;
use Throwable;

final class TestCaseTest extends TestCase
{
    public function testConstruct(): void
    {
        $test = new TestChildObject();
        $this->assertEquals([], $test->getArray());
        $this->assertEquals(0.0, $test->getFloat());
        $this->assertEquals(0, $test->getInt());
        $this->assertNull($test->getObject());
    }

    public function testConstructSetter(): void
    {
        $test = new TestChildObject([
            'array' => [1],
            'float' => 5.5,
            'int' => 3,
        ]);
        $this->assertEquals([1], $test->getArray());
        $this->assertEquals(5.5, $test->getFloat());
        $this->assertEquals(3, $test->getInt());
        $this->assertNull($test->getObject());
    }

    public function testConstructException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Config for sadsad not exists!');
        new TestChildObject(['sadsad' => 1]);
    }

    public function testAccessMethod(): void
    {
        $data = [
            'array' => [1],
            'float' => 5.5,
            'int' => 3,
        ];
        $test = new TestChildObject();
        $this->invokeMethod($test, 'setter', $data);
        $this->assertEquals([1], $test->getArray());
        $this->assertEquals(5.5, $test->getFloat());
        $this->assertEquals(3, $test->getInt());
        $this->assertNull($test->getObject());
    }

    public function testCreateInstanceWithoutConstructor(): void
    {
        $this->expectException(Error::class);
        $test = $this->createInstanceWithoutConstructor(TestChildObject::class);
        /** @phpstan-ignore-next-line */
        $this->assertEquals([], $test->getArray());
    }

    public function testCreateInstanceWithoutConstructorWrongClass(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Class asdsadasdsa not found');
        $this->createInstanceWithoutConstructor('asdsadasdsa');
    }

    public function testSetAndGetProperty(): void
    {
        $test = new TestChildObject();
        $this->setProperty($test, 'float', 4.5);
        $this->assertEquals(4.5, $this->getProperty($test, 'float'));
    }

    public function testGetProperty(): void
    {
        $test = new TestParentObject();
        $this->assertEquals('hidden', $this->getProperty($test, 'hiddenProperty'));
    }

    public function testGetPropertyException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not find property wrongAttribute');
        $test = new TestParentObject();
        $this->getProperty($test, 'wrongAttribute');
    }

    public function testTryTest(): void
    {
        $count = new stdClass();
        $count->counter = 0;
        $cb = static function () use ($count) {
            $count->counter++;
        };
        $this->tryTest($cb);
        $this->assertEquals(1, $count->counter);
    }

    public function testTryTestException(): void
    {
        $count = new stdClass();
        $count->counter = 0;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Too Many Loops!');
        $cb = static function () use ($count) {
            $count->counter++;
            throw new RuntimeException('Too Many Loops!');
        };

        try {
            $this->tryTest($cb);
        } catch (Throwable $t) {
            $this->assertEquals(3, $count->counter);
            throw $t;
        }
    }

    public function testLoopingTest(): void
    {
        $count = new stdClass();
        $count->counter = 0;
        $cb = static function () use ($count) {
            $count->counter++;
        };
        $this->loopingTest($cb);
        $this->assertEquals(5, $count->counter);
    }

    public function testLoopingTestException(): void
    {
        $count = new stdClass();
        $count->counter = 0;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Too Many Loops!');
        $cb = static function () use ($count) {
            $count->counter++;
            throw new RuntimeException('Too Many Loops!');
        };

        try {
            $this->loopingTest($cb);
        } catch (Throwable $t) {
            $this->assertEquals(5, $count->counter);
            throw $t;
        }
    }
}
