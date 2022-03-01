<?php

declare(strict_types=1);

namespace Milanowicz\Testing;

use PHPUnit\Framework\AssertionFailedError;
use Throwable;

final class ExceptionAssertionFailed extends AssertionFailedError implements
    ExceptionAssertionFailedInterface
{
    private array $data;

    public function __construct(
        string $message = '',
        array $data = [],
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
        $this->data = $data;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function toString(): string
    {
        if ($this->count() < 1) {
            return $this->getMessage();
        }

        return $this->getMessage() . PHP_EOL . print_r($this->data, true);
    }

    public function count(): int
    {
        return count($this->data);
    }
}
