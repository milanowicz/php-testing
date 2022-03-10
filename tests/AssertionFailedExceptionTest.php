<?php

declare(strict_types=1);

namespace Milanowicz\Testing;

use PHPUnit\Framework\TestCase;

final class AssertionFailedExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $t = new AssertionFailedException('TESTING');
        $this->assertEquals('TESTING', $t->getMessage());
        $this->assertEquals(0, $t->getCode());
        $this->assertNull($t->getPrevious());
        $this->assertStringContainsString('AssertionFailedExceptionTest.php', $t->getFile());
        $this->assertStringContainsString('AssertionFailedExceptionTest', $t->getSerializableTrace()[0]['class']);
        $this->assertStringContainsString('AssertionFailedExceptionTest', $t->getTrace()[0]['class'] ?? '');
        $this->assertEquals(13, $t->getLine());
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
            'array' => [
                'test' => 2
            ]
        ];

        $t = new AssertionFailedException('TESTING', $data);
        $this->assertEquals(
            'TESTING' . PHP_EOL . PHP_EOL
            . ' Data:' . PHP_EOL
            . '  asdsa => 1' . PHP_EOL
            . '  array:' . PHP_EOL
            . '    test => 2' . PHP_EOL . PHP_EOL,
            $t->toString()
        );
    }
}
