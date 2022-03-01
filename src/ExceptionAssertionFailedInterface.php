<?php

declare(strict_types=1);

namespace Milanowicz\Testing;

use PHPUnit\Framework\SelfDescribing;

interface ExceptionAssertionFailedInterface extends \Countable, SelfDescribing
{
    /**
     * Get an array.
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Return message or Message and Data, when data is greater than zero elements.
     *
     * @return string
     */
    public function toString(): string;
}
