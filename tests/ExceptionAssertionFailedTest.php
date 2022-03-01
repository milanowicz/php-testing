<?php

declare(strict_types=1);

namespace Milanowicz\Testing;

use PHPUnit\Framework\TestCase;

final class ExceptionAssertionFailedTest extends TestCase
{
    public function testConstruct(): void
    {
        $t = new ExceptionAssertionFailed('TESTING');
        $this->assertEquals('TESTING', $t->getMessage());
        $this->assertEquals(0, $t->getCode());
        $this->assertNull($t->getPrevious());
        $this->assertStringContainsString('ExceptionAssertionFailedTest.php', $t->getFile());
        $this->assertStringContainsString('ExceptionAssertionFailedTest', $t->getSerializableTrace()[0]['class']);
        $this->assertStringContainsString('ExceptionAssertionFailedTest', $t->getTrace()[0]['class'] ?? '');
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
        $t = new ExceptionAssertionFailed('TESTING', $data);
        $this->assertEquals(6, $t->count());
    }

    public function testToArray(): void
    {
        $data = [
            'asdsa' => 1,
            'asssssa' => 4231432
        ];
        $t = new ExceptionAssertionFailed('TESTING', $data);
        $this->assertEquals($data, $t->toArray());
        $this->assertEquals(2, $t->count());
        $this->assertEquals('TESTING', $t->getMessage());
        $this->assertEquals(0, $t->getCode());
        $this->assertNull($t->getPrevious());
    }

    public function testToString(): void
    {
        $t = new ExceptionAssertionFailed('TESTING');
        $this->assertEquals('TESTING', $t->toString());

        $data = [
            'asdsa' => 1
        ];
        $t = new ExceptionAssertionFailed('TESTING', $data);
        $this->assertEquals(
            'TESTING' . PHP_EOL . print_r($data, true),
            $t->toString()
        );
    }
}
