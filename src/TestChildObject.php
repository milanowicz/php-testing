<?php

declare(strict_types=1);

namespace Milanowicz\Testing;

use InvalidArgumentException;

/**
 * Child Test Object and only for testing.
 *
 * @infection-ignore-all
 */
class TestChildObject
{
    private string $hiddenProperty = 'hidden';

    public function __construct(
        array $config = [],
        protected ?object $object = null,
        protected array $array = [],
        protected int $int = 0,
        protected float $float = 0.0,
    ) {
        $this->setter($config);
    }

    /**
     * @throws InvalidArgumentException
     */
    private function setter(array $config): void
    {
        foreach ($config as $key => $value) {
            if (isset($this->{$key})) {
                $this->{$key} = $value;
            } else {
                throw new InvalidArgumentException(
                    'Config for ' . $key . ' not exists!'
                );
            }
        }
    }

    final public function getArray(): array
    {
        return $this->array;
    }

    final public function getInt(): int
    {
        return $this->int;
    }

    final public function getFloat(): float
    {
        return $this->float;
    }

    final public function getObject(): ?object
    {
        return $this->object;
    }
}
