<?php

declare(strict_types=1);

namespace Milanowicz\Testing;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use RuntimeException;
use Throwable;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @throws ReflectionException
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
     * @throws RuntimeException
     * @throws ReflectionException
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
     * @throws ReflectionException
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
     * @throws ReflectionException
     */
    final protected function setProperty(
        object $class,
        string $property,
        mixed $value = null
    ): ReflectionProperty {
        $method = new ReflectionProperty($class, $property);
        /** @infection-ignore-all */
        $this->accessible($method);
        if ($value !== null) {
            $method->setValue($class, $value);
        }
        return $method;
    }

    /**
     * @throws RuntimeException
     */
    final protected function getProperty(
        object $class,
        string $property
    ): mixed {
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
            return $reflectionProperty->getValue($class);
        }
        throw new RuntimeException('Could not find property ' . $property);
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
     * @throws Throwable
     */
    final protected function tryTest(
        callable $func,
        int $tries = 3
    ): void {
        do {
            try {
                $func();
                $tries = 0;
            } catch (Throwable $t) {
                $tries--;
                if ($tries < 1) {
                    throw $t;
                }
            }
        } while ($tries !== 0);
    }

    /**
     * @throws Throwable
     */
    final protected function loopingTest(
        callable $func,
        int $tries = 5
    ): void {
        $error = $tries;
        while ($tries > 0) {
            try {
                $func();
                $tries--;
            } catch (Throwable $t) {
                $error--;
                if ($error < 1) {
                    throw $t;
                }
            }
        }
    }
}
