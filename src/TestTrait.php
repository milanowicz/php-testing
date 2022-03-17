<?php

declare(strict_types=1);

namespace Milanowicz\Testing;

use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use RuntimeException;
use Throwable;

trait TestTrait
{
    /**
     * @throws \ReflectionException
     */
    final protected function accessMethod(
        object $class,
        string $method
    ): ReflectionMethod {
        $reflection = new ReflectionMethod($class, $method);
        /** @infection-ignore-all */
        $this->accessible($reflection);
        return $reflection;
    }

    /**
     * @throws AssertionFailedException
     * @throws Throwable
     */
    final protected function catchAssertionFailing(
        array $data,
        callable $function,
        string $catchData = ExpectationFailedException::class
    ): self {
        try {
            $function($data);
        } catch (Throwable $exception) {
            if ($exception instanceof $catchData) {
                throw new AssertionFailedException(
                    $exception->getMessage(),
                    $data,
                    $exception->getCode(),
                    $exception
                );
            }

            throw $exception;
        }
        return $this;
    }

    /**
     * @throws RuntimeException
     * @throws \ReflectionException
     */
    final protected function createInstanceWithoutConstructor(
        string $className
    ): object {
        if (!class_exists($className)) {
            throw new RuntimeException(
                sprintf('Class %s not found', $className)
            );
        }

        return (new ReflectionClass($className))->newInstanceWithoutConstructor();
    }

    /**
     * @throws \ReflectionException
     */
    final protected function invokeMethod(
        object $class,
        string $method,
        mixed ...$arguments
    ): mixed {
        return $this
            ->accessMethod($class, $method)
            ->invoke($class, ...$arguments);
    }

    /**
     * @throws RuntimeException
     */
    final protected function setProperty(
        object $class,
        string $property,
        mixed $value = null
    ): ReflectionProperty {
        $reflectionProperty = $this->getReflectionProperty($class, $property);
        if ($reflectionProperty !== null) {
            if ($value !== null) {
                $reflectionProperty->setValue($class, $value);
            }
            return $reflectionProperty;
        }
        throw new RuntimeException('Could not find property ' . $property);
    }

    /**
     * @throws RuntimeException
     */
    final protected function getProperty(
        object $class,
        string $property
    ): mixed {
        $reflectionProperty = $this->getReflectionProperty($class, $property);
        if ($reflectionProperty !== null) {
            return $reflectionProperty->getValue($class);
        }
        throw new RuntimeException('Could not find property ' . $property);
    }

    private function getReflectionProperty(
        object $class,
        string $property
    ): null|ReflectionProperty {
        $reflectionClass = new ReflectionClass($class);
        do {
            if ($reflectionClass->hasProperty($property)) {
                break;
            }
        } while ($reflectionClass = $reflectionClass->getParentClass());

        if ($reflectionClass !== false) {
            $reflectionProperty = $reflectionClass->getProperty($property);
            /** @infection-ignore-all */
            $this->accessible($reflectionProperty);
            return $reflectionProperty;
        }
        return null;
    }

    /**
     * @codeCoverageIgnore
     * @infection-ignore-all
     *
     * @see https://www.php.net/manual/en/reflectionmethod.setaccessible.php
     */
    private function accessible(
        ReflectionMethod|ReflectionProperty $reflection
    ): void {
        if (PHP_VERSION_ID < 80100) {
            $reflection->setAccessible(true);
        }
    }

    /**
     * @throws InvalidArgumentException
     * @throws Throwable
     */
    final protected function testLoops(
        callable $func,
        int $tries = 5,
        int $errors = 0
    ): array {
        $this
            ->checkNotToBeNegative($tries, '$tries')
            ->checkNotToBeNegative($errors, '$errors');
        $countTries = 0;
        $countErrors = 0;
        while ($tries > 0) {
            try {
                $func();
                $countTries++;
                $tries--;
            } catch (Throwable $t) {
                $countErrors++;
                if ($errors < 1) {
                    throw $t;
                }
                $errors--;
            }
        }
        return [$countTries, $countErrors];
    }

    /**
     * @throws InvalidArgumentException
     * @throws Throwable
     */
    final protected function tryTest(
        callable $func,
        int $tries = 3
    ): array {
        $this->checkNotToBeNegative($tries, '$tries');
        $counter = 0;
        $errors = 0;
        do {
            try {
                $func();
                $counter++;
                $tries = 0;
            } catch (Throwable $t) {
                $tries--;
                $errors++;
                if ($tries < 1) {
                    throw $t;
                }
            }
        } while ($tries !== 0);
        return [$counter, $errors];
    }

    /**
     * @throws InvalidArgumentException
     */
    private function checkNotToBeNegative(
        int $number,
        string $prefixMessage
    ): self {
        if ($number < 0) {
            throw new InvalidArgumentException($prefixMessage . ' could not be negative => ' . $number);
        }
        return $this;
    }
}
