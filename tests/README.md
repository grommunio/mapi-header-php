# MAPI Header PHP - Test Suite

This directory contains the PHPUnit test suite for the MAPI Header PHP library.

## Setup

Install PHPUnit via Composer:

```bash
composer require --dev phpunit/phpunit
```

## Running Tests

Run all tests:

```bash
vendor/bin/phpunit
```

Run specific test file:

```bash
vendor/bin/phpunit tests/TokenTest.php
```

Run with coverage (requires Xdebug):

```bash
vendor/bin/phpunit --coverage-html coverage
```

## Test Structure

- `bootstrap.php` - Test bootstrap file that loads the MAPI stub
- `UtilityFunctionsTest.php` - Tests for mapi.util.php functions
- `TokenTest.php` - Tests for the Token class

## Writing Tests

When writing new tests:

1. Extend `PHPUnit\Framework\TestCase`
2. Use type hints for method parameters and return types
3. Use descriptive test method names following `testMethodName()` convention
4. Add docblocks explaining complex test scenarios
5. Group related assertions together

## Coverage Goals

- Critical utility functions: 100%
- Token/KeyCloak classes: 90%+
- Exception classes: 80%+
- Complex recurrence classes: 70%+ (challenging due to MAPI dependencies)
