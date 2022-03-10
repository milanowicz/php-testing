<?php

declare(strict_types=1);

namespace Milanowicz\Testing;

use PHPUnit\Framework\AssertionFailedError;
use Throwable;

final class AssertionFailedException extends AssertionFailedError implements
    AssertionFailedExceptionInterface
{
    public function __construct(
        private string $title,
        private array $data = [],
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($this->toString(), $code, $previous);
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function toString(): string
    {
        if ($this->count() === 0) {
            return $this->title;
        }

        return $this->title
            . PHP_EOL
            . PHP_EOL
            . ' Data:'
            . PHP_EOL
            . $this->formatString($this->data)
            . PHP_EOL;
    }

    private function formatString(
        array &$data,
        string $output = '',
        int $steps = 2
    ): string {
        foreach ($data as $key => $value) {
            $output .= str_repeat(' ', $steps);
            if (is_array($value)) {
                $output .= $key . ':' . PHP_EOL;
                $output = $this->formatString($this->data[$key], $output, ($steps + 2));
            } else {
                $output .= $key . ' => ' . $value . PHP_EOL;
            }
        }
        return $output;
    }

    public function count(): int
    {
        return count($this->data);
    }
}
