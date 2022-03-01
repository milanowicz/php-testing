# PHP Thread
![Test](https://github.com/Milanowicz/php-testing/workflows/Testing/badge.svg?branch=master)
[![Mutation testing](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FMilanowicz%2Fphp-testing%2Fmain)](https://dashboard.stryker-mutator.io/reports/github.com/Milanowicz/php-testing/main)
[![codecov](https://codecov.io/gh/Milanowicz/php-testing/branch/master/graph/badge.svg?token=42G6ETI9NV)](https://codecov.io/gh/Milanowicz/php-testing)

![](https://img.shields.io/github/repo-size/milanowicz/php-testing)
![](https://img.shields.io/github/languages/code-size/milanowicz/php-testing)

PHP-Library for PHPUnit testing.


## Usage

Install by Composer

    $ composer require milanowicz/php-testing

Usage for abstract class TestCase:

    public function testSomething extends Milanowicz\Testing\TestCase
    {
        $this->accessMethod(ClassObject, MethodName);
        $this->createInstanceWithoutConstructor(ClassName);
        $this->invokeMethod(ClassObject, MethodName, Arguments for Method);
        $this->setProperty(ClassObject, PropertyName, PropertyValue);
        $this->getProperty(ClassObject, PropertyName);
        $this->tryTest(Function, NumberOfTries);
        $this->loopingTest(Function, NumberOfTries);
    }

Usage for ExceptionAssertionFailed to got data in Exception:

    $t = new ExceptionAssertionFailed(
        string $message = 'Testing',
        array $data = [1],
        int $code = 1,
        ?Throwable $previous = null,
    );
    $t->toArray(); // => [1]
    $t->count(); // => 1
    $t->toString(); // => Message and Data as String
    
    // and all Exception methods are available like this:
    $t->getMessage(); // => 'Testing'
    $t->getCode(); // => 1
    $t->getPrevious(); // => null OR a Throwable object

