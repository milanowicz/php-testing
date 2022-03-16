<?php

declare(strict_types=1);

namespace Milanowicz\Testing;

use Error;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
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
    public function testCatchAssertionFailing(stdClass $count): void
    {
        $data = [1, 2, 3, 4];
        $cb = static function () use ($count) {
            $count->counter++;
        };
        $this->catchAssertionFailing($data, $cb);
        $this->assertEquals(1, $count->counter);
    }

    /**
     * @dataProvider dataCountObject
     */
    public function testCatchAssertionFailingException(stdClass $count): void
    {
        $this->expectException(AssertionFailedException::class);
        $this->expectExceptionMessageMatches('/Hello Exception/');
        $this->expectExceptionMessageMatches('/Data:/');
        $data = ['a' => 1];
        $cb = static function () use ($count) {
            $count->counter++;
            throw new ExpectationFailedException('Hello Exception');
        };
        $this->catchAssertionFailing($data, $cb);
    }

    /**
     * @dataProvider dataCountObject
     */
    public function testCatchAssertionFailingError(stdClass $count): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessageMatches('/Hello Error/');
        $this->expectExceptionMessageMatches('/^(?!.*Data)/');
        $data = ['a' => 1];
        $cb = static function () use ($count) {
            $count->counter++;
            throw new Error('Hello Error');
        };
        $this->catchAssertionFailing($data, $cb);
    }

    /**
     * @dataProvider dataCountObject
     */
    public function testCatchErrorWithData(stdClass $count): void
    {
        $data = [1, 2, 3, 4];
        $cb = static function () use ($count) {
            $count->counter++;
        };
        $this->catchErrorWithData($data, $cb);
        $this->assertEquals(1, $count->counter);
    }

    /**
     * @dataProvider dataCountObject
     */
    public function testCatchErrorWithDataException(stdClass $count): void
    {
        $this->expectException(AssertionFailedException::class);
        $this->expectExceptionMessageMatches('/Hello Exception/');
        $this->expectExceptionMessageMatches('/Data:/');
        $data = ['a' => 1];
        $cb = static function () use ($count) {
            $count->counter++;
            throw new ExpectationFailedException('Hello Exception');
        };
        $this->catchErrorWithData($data, $cb);
    }

    /**
     * @dataProvider dataCountObject
     */
    public function testCatchErrorWithDataError(stdClass $count): void
    {
        $this->expectException(AssertionFailedException::class);
        $this->expectExceptionMessageMatches('/Hello Error/');
        $this->expectExceptionMessageMatches('/Data:/');
        $data = ['a' => 1];
        $cb = static function () use ($count) {
            $count->counter++;
            throw new Error('Hello Error');
        };
        $this->catchErrorWithData($data, $cb);
    }

    /**
     * @dataProvider dataCountObject
     */
    public function testTestLoops(stdClass $count): void
    {
        $cb = static function () use ($count) {
            $count->counter++;
        };
        $result = $this->testLoops($cb);
        $this->assertEquals(5, $count->counter);
        $this->assertEquals([5, 0], $result);
    }

    /**
     * @dataProvider dataCountObject
     */
    public function testTestLoopsAndCountErrors(stdClass $count): void
    {
        $cb = static function () use ($count) {
            $count->counter++;
            if ($count->counter === 1) {
                throw new RuntimeException('Too Many Loops!');
            }
        };

        $result = $this->testLoops($cb, 1, 1);
        $this->assertEquals([1, 1], $result);
    }

    /**
     * @dataProvider dataCountObject
     */
    public function testTestLoopsErrors(stdClass $count): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Too Many Loops!');
        $cb = static function () use ($count) {
            $count->counter++;
            if ($count->counter === 2) {
                throw new RuntimeException('Too Many Loops!');
            }
        };

        try {
            $this->testLoops($cb, 3);
        } catch (RuntimeException $t) {
            $this->assertEquals(2, $count->counter);
            throw $t;
        }
    }

    /**
     * @dataProvider dataCountObject
     */
    public function testTestLoopsExceptionManyLoops(stdClass $count): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Too Many Loops!');
        $cb = static function () use ($count) {
            $count->counter++;
            throw new RuntimeException('Too Many Loops!');
        };

        try {
            $this->testLoops($cb, 5, 4);
        } catch (RuntimeException $t) {
            $this->assertEquals(5, $count->counter);
            throw $t;
        }
    }

    /**
     * @dataProvider dataCountObject
     */
    public function testTestLoopInvalidArgumentExceptionForErrors(stdClass $count): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$errors could not be negative => -1');
        $cb = static function () use ($count) {
            $count->counter++;
            throw new RuntimeException('Too Many Loops!');
        };

        $this->testLoops($cb, 3, -1);
    }

    /**
     * @dataProvider dataCountObject
     */
    public function testTestLoopInvalidArgumentExceptionForTries(stdClass $count): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$tries could not be negative => -1');
        $cb = static function () use ($count) {
            $count->counter++;
            throw new RuntimeException('Too Many Loops!');
        };

        $this->testLoops($cb, -1);
    }

    /**
     * @dataProvider dataCountObject
     */
    public function testTryTest(stdClass $count): void
    {
        $cb = static function () use ($count) {
            $count->counter++;
        };
        $result = $this->tryTest($cb);
        $this->assertEquals(1, $count->counter);
        $this->assertEquals([1, 0], $result);
    }

    /**
     * @dataProvider dataCountObject
     */
    public function testTryTestAndCountErrors(stdClass $count): void
    {
        $cb = static function () use ($count) {
            $count->counter++;
            if ($count->counter === 1) {
                throw new RuntimeException('Too Many Loops!');
            }
        };

        $result = $this->tryTest($cb, 2);
        $this->assertEquals([1, 1], $result);
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
    public function testTryTestInvalidArgumentExceptionForTries(stdClass $count): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$tries could not be negative => -1');
        $cb = static function () use ($count) {
            $count->counter++;
            throw new RuntimeException('Too Many Loops!');
        };

        $this->tryTest($cb, -1);
    }
}
