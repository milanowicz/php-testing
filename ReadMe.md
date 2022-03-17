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


### Methods

Example for using abstract class in your ClassTest:

```php
class UnitTest extends Milanowicz\Testing\TestCase
{
    public function testMethod(): void
    {
        // All Traits in abstract Milanowicz\Testing\TestCase are extends!
        // See below for further details from Trait methods
    }
}
```

OR import Trait(s) for use them:

```php
class UnitTest extends What\Ever\TestCase
{
    use Milanowicz\Testing\TestTrait;

    public function testMethod(): void
    {
        // Access to private or protected Method
        $this->accessMethod((object) Class, (string) Method);
        // Run test(s) in it and when an assertion failed would throw, see message and data for the reason
        $this->catchAssertionFailing((array) $data, function ($data) {
            $this->assertEquals('whatEver', $data['whatEver']);
        }, (string) $catchData = ExpectationFailedException::class);
        // Create class without constructor
        $this->createInstanceWithoutConstructor((string) Class);
        // Call a private or protected Method with argument(s)
        $this->invokeMethod((object) Class, (string) Method, (mixed) ArgumentsForMethod);
        // Set a value to private or protected property 
        $this->setProperty((object) Class, (string) Property, (mixed) PropertyValue);
        // Get a value from a private or protected property back
        $this->getProperty((object) Class, (string) Property);
        // Execute test of number tries to check, if it runs multiply times successfully
        $this->testLoops((callable) Function, (int) NumberOfTries, (int) NumberOfErrors);
        // Execute test and when it's throw an exception, then try it again
        $this->tryTest((callable) Function, (int) NumberOfTries);
    }
}

```

TestPerformanceTrait is to execute two functions and see which was faster of those:

```php
class UnitTest extends What\Ever\TestCase
{
    use Milanowicz\Testing\TestPerformanceTrait;

    public function testMethod(): void
    {
        // Example functions
        $cb1 = static function () {
            usleep(100);
        };
        $cb2 = static function () {
            usleep(200);
        };

        // Call both methods after each other and save run times from them
        $this->measureTime((callable) $cb1, (callable) $cb2, (int) $n = 20);
        // Check AVG and Student-Test
        $this->checkPerformance((bool) $function1 = true, (float) $pValue = 0.05);
        // $cb1 should be faster 
        $this->checkMeanTime((bool) $function1 = true);
        // $cb2 should be slower and throw an Exception 
        $this->checkMeanTime((bool) false);
        // Check if $cb1 is significant faster
        $this->checkStudentTest((float) $pValue = 0.05);
        // Get all time measures
        $this->getTimeMeasures();
        // Get both in array with AVG, STD and Median 
        $this->getTimeStats();
        // Get result from Student-T test 
        $this->getTimeSignificance();
        // Clear all times and measures
        $this->clearTimes(); 
    }
}
```


### Exception

Usage for AssertionFailedException to get data in Exception message:

```php
$e = new AssertionFailedException(
    (string) $message = 'Testing',
    (array) $data = [1],
    (int) $code = 1,
    (null|Throwable) $previous = null,
);
$e->toArray(); // => [1]
$e->count(); // => 1
$e->toString(); // => Message and Data as String

// and all Exception methods are available:
$e->getMessage(); // => Message and Data as String
$e->getCode(); // => 1
$e->getPrevious(); // => null OR a Throwable object
```


## Development

Run all test suites
```shell
> composer tests
```

Run PHP Code Styling
```shell
> composer style
```

Run PHPStan to analyze code
```shell
> composer analyze
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
