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
> composer require --dev milanowicz/php-testing
```

Usage for abstract class TestCase:

```php
class ClassTest extends Milanowicz\Testing\TestCase
    public function testSomething(): void
    {
        $this->accessMethod((object) Class, (string) Method);
        $this->createInstanceWithoutConstructor((string) Class);
        $this->invokeMethod((object) Class, (string) Method, (mixed) ArgumentsForMethod);
        $this->setProperty((object) Class, (string) Property, (mixed) PropertyValue);
        $this->getProperty((object) Class, (string) Property);
        $this->tryTest((callable) Function, (int) NumberOfTries);
        $this->loopingTest((callable) Function, (int) NumberOfTries);
        // and all other Traits too, see below
    }
}
```

OR import Trait(s) for using:

```php
class ClassTest extends What\Ever\TestCase
    use Milanowicz\Testing\TestTrait;

    public function testSomething(): void
    {
        $this->accessMethod((object) Class, (string) Method);
        $this->createInstanceWithoutConstructor((string) Class);
        $this->invokeMethod((object) Class, (string) Method, (mixed) ArgumentsForMethod);
        $this->setProperty((object) Class, (string) Property, (mixed) PropertyValue);
        $this->getProperty((object) Class, (string) Property);
        $this->tryTest((callable) Function, (int) NumberOfTries);
        $this->loopingTest((callable) Function, (int) NumberOfTries);
    }
}

```

TestPerformanceTrait is to execute two functions and see which was faster of those:

```php
class ClassTest extends What\Ever\TestCase
    use Milanowicz\Testing\TestPerformanceTrait;

    public function testSomething(): void
    {
        $cb1 = static function () {
            usleep(100);
        };
        $cb2 = static function () {
            usleep(200);
        };

        $this->measurePerformanceTime($cb1, $cb2);
        $this->checkPerformanceTime(); // $cb1 should be faster 
        $this->checkPerformanceTime(false); // $cb2 should be slower and throw an Exception
        $this->checkStudentTest(); // Check if $cb1 is significant faster

        $this->getPerformanceMeasures(); // Get all time measures 
        $this->getPerformanceStats(); // Get both in array with AVG, STD and Median 
    }
}
```

Usage for ExceptionAssertionFailed to got data in Exception:

```php
$t = new ExceptionAssertionFailed(
    (string) $message = 'Testing',
    (array) $data = [1],
    (int) $code = 1,
    (null|Throwable) $previous = null,
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
> composer tests
```

Run PHP CS Fixer to code styling
```shell
> composer style
```

Run PHPStan to analyze
```shell
> composer anlayze
```

Run PHPUnit tests
```shell
> composer test
```

Run Mutation tests by Infection
```shell
> composer infection
```


## License

[GNU GPL Version 3](http://www.gnu.org/copyleft/gpl.html)
