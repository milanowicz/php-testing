# PHP Testing
![Test](https://github.com/milanowicz/php-testing/workflows/Testing/badge.svg?branch=master)
[![Mutation testing](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fmilanowicz%2Fphp-testing%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/milanowicz/php-testing/master)
[![codecov](https://codecov.io/gh/milanowicz/php-testing/branch/master/graph/badge.svg?token=42G6ETI9NV)](https://codecov.io/gh/milanowicz/php-testing)

![](https://img.shields.io/packagist/php-v/milanowicz/php-testing)
![](https://img.shields.io/github/languages/top/milanowicz/php-testing)

![](https://img.shields.io/github/v/tag/milanowicz/php-testing)
![](https://img.shields.io/github/repo-size/milanowicz/php-testing)
![](https://img.shields.io/github/languages/code-size/milanowicz/php-testing)

![](https://img.shields.io/packagist/v/milanowicz/php-testing)
![](https://img.shields.io/packagist/dt/milanowicz/php-testing)
![](https://img.shields.io/packagist/dd/milanowicz/php-testing)
![](https://img.shields.io/packagist/dm/milanowicz/php-testing)


Library for PHPUnit testing.


## Usage

Install by Composer

```shell
$ composer require --dev milanowicz/php-testing
```

Usage for abstract class TestCase:

```php
class TestCaseTest extends Milanowicz\Testing\TestCase
    public function testSomething
    {
        $this->accessMethod(ClassObject, MethodName);
        $this->createInstanceWithoutConstructor(ClassName);
        $this->invokeMethod(ClassObject, MethodName, Arguments for Method);
        $this->setProperty(ClassObject, PropertyName, PropertyValue);
        $this->getProperty(ClassObject, PropertyName);
        $this->tryTest(Function, NumberOfTries);
        $this->loopingTest(Function, NumberOfTries);
    }
}
```

OR import Trait for using it:

```php
class TestCaseTest extends What\Ever\TestCase
    use Milanowicz\Testing\TestTrait;

    public function testSomething
    {
        $this->accessMethod(ClassObject, MethodName);
        $this->createInstanceWithoutConstructor(ClassName);
        $this->invokeMethod(ClassObject, MethodName, Arguments for Method);
        $this->setProperty(ClassObject, PropertyName, PropertyValue);
        $this->getProperty(ClassObject, PropertyName);
        $this->tryTest(Function, NumberOfTries);
        $this->loopingTest(Function, NumberOfTries);
    }
}
```

Usage for ExceptionAssertionFailed to got data in Exception:

```php
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
```


## Development

Run all test suites
```shell
$ composer tests
```

Run PHP CS Fixer to code styling
```shell
$ composer style
```

Run PHPStan to analyze
```shell
$ composer anlayze
```

Run PHPUnit tests
```shell
$ composer test
```

Run Mutation tests by Infection
```shell
$ composer infection
```


## License

[GNU GPL Version 3](http://www.gnu.org/copyleft/gpl.html)
