<?php

declare(strict_types=1);

namespace Milanowicz\Testing;

use PHPUnit\Framework\TestCase;
use Prophecy\Exception\Prophecy\MethodProphecyException;
use Prophecy\PhpUnit\ProphecyTrait;

final class AssertionFailedExceptionTest extends TestCase
{
    use ProphecyTrait;

    public function testConstruct(): void
    {
        $t = new AssertionFailedException('TESTING');
        $this->assertEquals('TESTING', $t->getMessage());
        $this->assertEquals(0, $t->getCode());
        $this->assertNull($t->getPrevious());
        $this->assertStringContainsString('AssertionFailedExceptionTest.php', $t->getFile());
        $this->assertStringContainsString('AssertionFailedExceptionTest', $t->getSerializableTrace()[0]['class']);
        $this->assertStringContainsString('AssertionFailedExceptionTest', $t->getTrace()[0]['class'] ?? '');
        $this->assertGreaterThan(13, $t->getLine());
    }

    public function testCount(): void
    {
        $data = [
            'asdsa' => 1,
            'frwet' => 1,
            'sfsdfg' => 1,
            'dfsf' => 1,
            'dfdsfgsd' => 1,
            'asssssa' => 4231432
        ];
        $t = new AssertionFailedException('TESTING', $data);
        $this->assertEquals(6, $t->count());
    }

    public function testToArray(): void
    {
        $data = [
            'asdsa' => 1,
            'asssssa' => 4231432
        ];
        $t = new AssertionFailedException('TESTING', $data);
        $this->assertEquals($data, $t->toArray());
        $this->assertEquals(2, $t->count());
        $this->assertEquals($t->toString(), $t->getMessage());
        $this->assertEquals(0, $t->getCode());
        $this->assertNull($t->getPrevious());
    }

    public function testToString(): void
    {
        $t = new AssertionFailedException('TESTING');
        $this->assertEquals('TESTING', $t->toString());

        $data = [
            'asdsa' => 1,
            'sdsdsds' => 1,
            'array' => [
                'test' => 2,
                'test2' => 3,
            ]
        ];

        $t = new AssertionFailedException('TESTING', $data);
        $this->assertEquals(
            'TESTING' . PHP_EOL . PHP_EOL
            . ' Data:' . PHP_EOL
            . '  asdsa => 1' . PHP_EOL
            . '  sdsdsds => 1' . PHP_EOL
            . '  array:' . PHP_EOL
            . '    test => 2' . PHP_EOL
            . '    test2 => 3' . PHP_EOL . PHP_EOL,
            $t->toString()
        );
    }

    public function testProphesizeInterface(): void
    {
        $exception = $this->prophesize(AssertionFailedExceptionInterface::class);
        $exception->count()->willReturn(2);
        $exception->toArray()->willReturn([1, 2]);
        $exception->toString()->willReturn('HELLO Message');

        $class = new TestParentObject(object: $exception->reveal());

        $this->assertEquals(2, $class->getObject()->count());
        $this->assertEquals([1, 2], $class->getObject()->toArray());
        $this->assertEquals('HELLO Message', $class->getObject()->toString());
    }

    public function testProphesizeInterfaceError(): void
    {
        $this->expectException(MethodProphecyException::class);
        $this->expectExceptionMessageMatches('/Can not add prophecy for a method/');

        $exception = $this->prophesize(AssertionFailedExceptionInterface::class);
        $exception->getMessage()->willReturn('HELLO Message');

        new TestParentObject(object: $exception->reveal());
    }
}
