<?php

declare(strict_types=1);

namespace Milanowicz\Testing;

/**
 * Parent Test Object and only for testing.
 *
 * @infection-ignore-all
 */
final class TestParentObject extends TestChildObject
{
    public function __construct(
        array $config = [],
        protected ?object $object = null,
        protected array $array = [],
        protected int $int = 0,
        protected float $float = 0.0,
    ) {
        parent::__construct(
            $config,
            $this->object,
            $this->array,
            $this->int,
            $this->float
        );
    }
}
