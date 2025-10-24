# MAPI Header PHP - Test Suite

This directory contains the comprehensive PHPUnit test suite for the MAPI Header
PHP library.

## Table of Contents

- [Setup](#setup)
- [Running Tests](#running-tests)
- [Test Structure](#test-structure)
- [Writing Tests](#writing-tests)
- [Coverage Goals](#coverage-goals)
- [Testing Strategy](#testing-strategy)

## Setup

### Installing Dependencies

Install PHPUnit and development dependencies via Composer:

```bash
composer install --dev
```

This installs:
- PHPUnit 9.x/10.x (depending on PHP version)
- PHP-CS-Fixer for code style
- Rector for compatibility checks

### MAPI Stub

The test suite uses a PHP MAPI stub (`dev/php-mapi-stub.php`) that simulates the
MAPI extension without requiring the actual extension to be installed. This
allows tests to run in CI/CD environments.

## Running Tests

### All Tests

Run the complete test suite:

```bash
vendor/bin/phpunit
```

### Specific Test File

Run a single test file:

```bash
vendor/bin/phpunit tests/TokenTest.php
vendor/bin/phpunit tests/BaseExceptionTest.php
```

### Specific Test Method

Run a specific test:

```bash
vendor/bin/phpunit --filter testTokenParsing tests/TokenTest.php
```

### Test Groups

Run tests by group (if defined):

```bash
vendor/bin/phpunit --group authentication
vendor/bin/phpunit --exclude-group slow
```

### With Coverage

Generate HTML coverage report (requires Xdebug or PCOV):

```bash
vendor/bin/phpunit --coverage-html coverage
```

Generate text coverage summary:

```bash
vendor/bin/phpunit --coverage-text
```

Generate Clover XML for CI:

```bash
vendor/bin/phpunit --coverage-clover coverage.xml
```

### Verbose Output

See detailed test progress:

```bash
vendor/bin/phpunit --verbose
vendor/bin/phpunit --debug
```

### Configuration

Tests use `phpunit.xml` in the root directory. Key settings:

- Strict mode enabled (fails on warnings, risky tests)
- Fails on empty test suites
- Suppresses output during test execution
- Tracks coverage for all `.php` files except vendor/tests/dev

## Test Structure

### Test Files

| File | Purpose | Coverage |
|------|---------|----------|
| `bootstrap.php` | Test bootstrap, loads MAPI stub | N/A |
| `BaseExceptionTest.php` | Tests for BaseException class | Exception handling |
| `MAPIExceptionTest.php` | Tests for MAPIException class | MAPI error mapping |
| `TokenTest.php` | Tests for Token (JWT) class | Token parsing |
| `ExtendedTokenTest.php` | Extended JWT functionality | Advanced tokens |
| `KeyCloakTest.php` | Tests for KeyCloak SSO | Authentication |
| `FreeBusyTest.php` | Tests for FreeBusy utilities | Free/busy ops |
| `UtilityFunctionsTest.php` | Tests for mapi.util.php | Utility functions |

### Directory Structure

```
tests/
├── bootstrap.php           # PHPUnit bootstrap
├── README.md              # This file
├── BaseExceptionTest.php
├── MAPIExceptionTest.php
├── TokenTest.php
├── ExtendedTokenTest.php
├── KeyCloakTest.php
├── FreeBusyTest.php
└── UtilityFunctionsTest.php
```

## Writing Tests

### Basic Test Structure

Follow these conventions when writing tests:

```php
<?php
use PHPUnit\Framework\TestCase;

class MyFeatureTest extends TestCase {
    /**
     * @var MyClass
     */
    private $instance;

    protected function setUp(): void {
        parent::setUp();
        $this->instance = new MyClass();
    }

    protected function tearDown(): void {
        $this->instance = null;
        parent::tearDown();
    }

    /**
     * Test that myMethod returns expected value.
     */
    public function testMyMethodReturnsValue(): void {
        $result = $this->instance->myMethod();

        $this->assertIsString($result);
        $this->assertEquals('expected', $result);
    }

    /**
     * Test that myMethod throws exception on invalid input.
     */
    public function testMyMethodThrowsExceptionOnInvalidInput(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->instance->myMethod(null);
    }
}
```

### Naming Conventions

1. **Test Class Names**: `{ClassName}Test.php`
   - `TokenTest.php` for testing `Token` class
   - `BaseExceptionTest.php` for testing `BaseException` class

2. **Test Method Names**: `test{MethodName}{Scenario}()`
   - `testTokenParsing()` - tests parsing functionality
   - `testTokenThrowsExceptionOnInvalidFormat()` - tests exception scenario
   - `testIsExpiredReturnsTrueWhenExpired()` - descriptive scenario

3. **Use descriptive names** that explain what's being tested

### Type Hints

Always use type hints:

```php
public function testSomething(): void {
    $value = $this->getValue();
    $this->assertIsInt($value);
}

private function getValue(): int {
    return 42;
}
```

### Assertions

Use the most specific assertion available:

```php
// Good
$this->assertSame(10, $value);           // Strict type checking
$this->assertEquals('foo', $result);      // Value equality
$this->assertInstanceOf(Token::class, $obj);
$this->assertTrue($condition);
$this->assertNull($value);

// Avoid
$this->assertEquals(true, $condition);    // Use assertTrue instead
$this->assertEquals(null, $value);        // Use assertNull instead
```

### Testing Exceptions

```php
public function testExceptionThrown(): void {
    $this->expectException(MAPIException::class);
    $this->expectExceptionMessage('Invalid token');
    $this->expectExceptionCode(MAPI_E_INVALID_PARAMETER);

    $obj->methodThatThrows();
}
```

### Data Providers

Use data providers for testing multiple scenarios:

```php
/**
 * @dataProvider validTokenProvider
 */
public function testValidTokenParsing(string $token, array $expected): void {
    $tokenObj = new Token($token);
    $claims = $tokenObj->get_claims();

    $this->assertEquals($expected, $claims);
}

public function validTokenProvider(): array {
    return [
        'simple token' => [
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...',
            ['sub' => '1234567890', 'name' => 'John Doe']
        ],
        'expired token' => [
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...',
            ['sub' => '0987654321', 'exp' => 1234567890]
        ],
    ];
}
```

### Mocking

For classes that depend on MAPI functions, use the stub:

```php
public function testSomethingWithMAPI(): void {
    // The bootstrap.php loads dev/php-mapi-stub.php
    // which provides mock MAPI functions

    // Your test code here
}
```

### DocBlocks

Add clear documentation:

```php
/**
 * Test that Token::is_expired() returns true when the token has expired.
 *
 * This test creates a token with an expiration time in the past and
 * verifies that is_expired() correctly identifies it as expired.
 */
public function testIsExpiredReturnsTrueForExpiredToken(): void {
    // ...
}
```

### Grouping Assertions

Group related assertions together:

```php
public function testTokenProperties(): void {
    $token = new Token($validJWT);

    // Test all token components are set
    $this->assertNotNull($token->token_header);
    $this->assertNotNull($token->token_payload);
    $this->assertNotNull($token->token_signature);

    // Test header structure
    $header = $token->token_header;
    $this->assertArrayHasKey('typ', $header);
    $this->assertArrayHasKey('alg', $header);
    $this->assertEquals('JWT', $header['typ']);
}
```

## Coverage Goals

### Target Coverage Levels

| Component | Target | Rationale |
|-----------|--------|-----------|
| Critical utility functions | **100%** | Core functionality |
| Token/KeyCloak classes | **90%+** | Authentication critical |
| Exception classes | **80%+** | Error handling paths |
| Recurrence classes | **70%+** | Complex MAPI dependencies |
| Meeting request classes | **70%+** | Complex MAPI dependencies |

### Current Coverage

Run this to see current coverage:

```bash
vendor/bin/phpunit --coverage-text --colors=never | grep -A 3 "Code Coverage Report"
```

### Improving Coverage

1. **Identify gaps**:
   ```bash
   vendor/bin/phpunit --coverage-html coverage
   # Open coverage/index.html in browser
   ```

2. **Add tests** for uncovered lines

3. **Focus on**:
   - Edge cases
   - Error conditions
   - Boundary values
   - Exception paths

## Testing Strategy

### Unit Tests

Test individual methods in isolation:

```php
public function testCalculateNextOccurrence(): void {
    $recurrence = new Recurrence($store, $message);
    $next = $recurrence->calculateNextOccurrence($baseDate);

    $this->assertGreaterThan($baseDate, $next);
}
```

### Integration Tests

Test interactions between classes:

```php
public function testMeetingRequestWithRecurrence(): void {
    $mr = new Meetingrequest($store, $message, $session);
    $recurrence = new Recurrence($store, $message);

    // Test that meeting request properly handles recurring meetings
    // ...
}
```

### Edge Cases

Always test:

- Null/empty inputs
- Boundary values (min/max)
- Invalid data formats
- Missing required properties
- Malformed data

### Regression Tests

When fixing bugs:

1. Write a test that reproduces the bug
2. Verify the test fails
3. Fix the bug
4. Verify the test passes
5. Keep the test to prevent regression

## Continuous Integration

### GitHub Actions / GitLab CI

Example CI configuration:

```yaml
test:
  script:
    - composer install --dev
    - vendor/bin/phpunit --coverage-text --colors=never
    - vendor/bin/php-cs-fixer fix --dry-run --diff
```

### Local Pre-commit Hook

Add to `.git/hooks/pre-commit`:

```bash
#!/bin/bash
vendor/bin/phpunit
if [ $? -ne 0 ]; then
    echo "Tests failed. Commit aborted."
    exit 1
fi
```

## Troubleshooting

### Tests Not Found

Ensure `phpunit.xml` is in the root directory and contains:

```xml
<testsuites>
    <testsuite name="MAPI Header PHP Test Suite">
        <directory>tests</directory>
    </testsuite>
</testsuites>
```

### MAPI Functions Not Defined

The `tests/bootstrap.php` should load `dev/php-mapi-stub.php`. Verify:

```bash
grep "php-mapi-stub.php" tests/bootstrap.php
```

### Coverage Requires Xdebug

Install Xdebug:

```bash
pecl install xdebug
```

Or use PCOV (faster):

```bash
pecl install pcov
```

## Contributing Tests

When contributing tests:

1. Follow the naming conventions above
2. Ensure tests are isolated (no dependencies on other tests)
3. Use type hints and docblocks
4. Add data providers for multiple scenarios
5. Test both success and failure paths
6. Update this README if adding new test categories

## Resources

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [PHPUnit Assertions](https://phpunit.de/manual/current/en/appendixes.assertions.html)
- [Best Practices](https://phpunit.de/manual/current/en/writing-tests-for-phpunit.html)
