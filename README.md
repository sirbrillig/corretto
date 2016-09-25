**Corretto** is a simple, fast PHP test runner.

Modeled after [mocha](https://mochajs.org/), Corretto provides a DSL (domain-specific language) that makes writing tests easy and fast. After all, wouldn't you rather spend most of your effort writing your code rather than writing your tests?

```php
<?php
use function \Corretto\{describe, it, assertTrue, assertFalse};

describe( 'isFive()', function() {
	it( 'returns true if its argument is five', function() {
		assertTrue( isFive( 5 ) );
	} );

	it( 'returns false if its argument is not five', function() {
		assertFalse( isFive( 6 ) );
	} );
} );
```

<img src="./examples/is-five.png">

## Custom Assertions

Writing custom assertions is easy too. Anything that throws an `Exception` counts as a test failure!

(You can also throw `\Corretto\AssertionFailure` which will provide slightly less noisy failures.)

## Examples

```php
<?php

use function \Corretto\{describe, it};
use function \Corretto\{test, suite};
use function \Corretto\{context, specify};
use function \Corretto\{assertTrue, assertFalse, assertEquals, assertNotEquals};
use function \Corretto\{expect};
use const \Corretto\SKIP;

it( 'allows tests outside a suite', function() {
	assertTrue( true );
} );

test( 'tests can use "test" as well as "it"', function() {
	assertTrue( true );
} );

specify( 'tests can use "specify" as well as "it"', function() {
	assertTrue( true );
} );

describe( 'describe()', function() {
	describe( 'when nested', function() {
		describe( 'more than once', function() {
			it( 'passes if its argument is true', function() {
				assertTrue( true );
			} );
		} );
		it( 'skips tests with no function' );
		it( SKIP, 'skips tests with the SKIP constant as the first argument', function() {
			assertTrue( false );
		} );
		it( 'passes if its argument is true', function() {
			assertTrue( true );
		} );
	} );

	it( 'supports non-nested tests along with nested ones', function() {
		assertTrue( true );
	} );

	describe( 'when multiple tests are nested at the same level', function() {
		it( 'passes if its argument is true', function() {
			assertTrue( true );
		} );
	} );

	describe( SKIP, 'allows skipping whole suites', function() {
		it( 'passes if its argument is true', function() {
			assertTrue( false );
		} );
	} );
} );

context( 'a bunch of tests', function() {
	specify( 'suites can use "context" as well as "describe"', function() {
		assertTrue( true );
	} );
} );

suite( 'my tests', function() {
	test( 'suites can use "suite" as well as "describe"', function() {
		assertTrue( true );
	} );

	suite( 'there are many assertions', function() {
		test( 'assertEquals()', function() {
			$actual = 'expected';
			assertEquals( 'expected', $actual );
		} );

		test( 'assertNotEquals()', function() {
			$actual = 'actual';
			assertNotEquals( 'expected', $actual );
		} );

		test( 'assertTrue()', function() {
			assertTrue( true );
		} );

		test( 'assertFalse()', function() {
			assertFalse( false );
		} );
	} );

	suite( 'expectation syntax also works for assertions', function() {
		suite( 'expect()', function() {
			test( '->toBeTrue()', function() {
				expect( true )->toBeTrue();
			} );

			test( '->toBeFalse()', function() {
				expect( false )->toBeFalse();
			} );

			test( '->toEqual()', function() {
				expect( 'hi' )->toEqual( 'hi' );
			} );

			test( '->toNotEqual()', function() {
				expect( 'hi' )->toNotEqual( 'bye' );
			} );
		} );
	} );
} );
```
