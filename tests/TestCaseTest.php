<?php

declare(strict_types=1);

namespace Milanowicz\Testing;

use Error;
use InvalidArgumentException;
use RuntimeException;
use stdClass;

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

    public function testConstructException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Config for sadsad not exists!');
        new TestChildObject(['sadsad' => 1]);
    }

    public function dataTestChildObject(): array
    {
        return [
            [[
                'array' => [1],
                'float' => 5.5,
                'int' => 3,
            ]], [[
                'array' => [1, 'asd'],
                'float' => -5,
                'int' => -3,
            ]]
        ];
    }

    /**
     * @dataProvider dataTestChildObject
     */
    public function testConstructSetter(array $data): void
    {
        $test = new TestChildObject($data);
        $this->assertEquals($data['array'], $test->getArray());
        $this->assertEquals($data['float'], $test->getFloat());
        $this->assertEquals($data['int'], $test->getInt());
        $this->assertNull($test->getObject());
    }

    /**
     * @dataProvider dataTestChildObject
     */
    public function testAccessMethod(array $data): void
    {
        $test = new TestParentObject();
        $this->invokeMethod($test, 'setter', $data);
        $this->assertEquals($data['array'], $test->getArray());
        $this->assertEquals($data['float'], $test->getFloat());
        $this->assertEquals($data['int'], $test->getInt());
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

        $this->setProperty($test, 'hiddenProperty', 'showIt');
        $this->assertEquals('showIt', $this->getProperty($test, 'hiddenProperty'));
    }

    public function testSetPropertyByParent(): void
    {
        $test = new TestParentObject();
        $this->setProperty($test, 'hiddenProperty', 'showIt');
        $this->assertEquals('showIt', $this->getProperty($test, 'hiddenProperty'));
    }

    public function testSetPropertyByParentException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not find property wrongAttribute');

        $test = new TestParentObject();
        $this->setProperty($test, 'wrongAttribute', 'showIt');
    }

    public function testGetPropertyException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not find property wrongAttribute');
        $test = new TestParentObject();
        $this->getProperty($test, 'wrongAttribute');
    }

    public function dataCountObject(): array
    {
        $count = new stdClass();
        $count->counter = 0;
        return [[$count]];
    }

    /**
     * @dataProvider dataCountObject
     */
    public function testTryTest(stdClass $count): void
    {
        $cb = static function () use ($count) {
            $count->counter++;
        };
        $this->tryTest($cb);
        $this->assertEquals(1, $count->counter);
    }

    /**
     * @dataProvider dataCountObject
     */
    public function testTryTestException(stdClass $count): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Too Many Loops!');
        $cb = static function () use ($count) {
            $count->counter++;
            throw new RuntimeException('Too Many Loops!');
        };

        try {
            $this->tryTest($cb);
        } catch (RuntimeException $t) {
            $this->assertEquals(3, $count->counter);
            throw $t;
        }
    }

    /**
     * @dataProvider dataCountObject
     */
    public function testLoopingTest(stdClass $count): void
    {
        $cb = static function () use ($count) {
            $count->counter++;
        };
        $this->loopingTest($cb);
        $this->assertEquals(5, $count->counter);
    }

    /**
     * @dataProvider dataCountObject
     */
    public function testLoopingTestException(stdClass $count): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Too Many Loops!');
        $cb = static function () use ($count) {
            $count->counter++;
            throw new RuntimeException('Too Many Loops!');
        };

        try {
            $this->loopingTest($cb);
        } catch (RuntimeException $t) {
            $this->assertEquals(5, $count->counter);
            throw $t;
        }
    }
}
