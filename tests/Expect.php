<?php

use function \Corretto\describe;
use function \Corretto\it;
use function \Corretto\expect;

describe( 'expect()', function() {
	describe( 'toBeTrue()', function() {
		it( 'passes if the actual is true', function() {
			expect( true )->toBeTrue();
		} );

		it( 'fails if its argument is false', function() {
			try {
				expect( false )->toBeTrue();
			} catch ( Exception $e ) {
				return;
			}
			throw new Exception( 'toBeTrue() did not fail as expected' );
		} );

		it( 'fails with an expected string if its argument is false', function() {
			try {
				expect( false )->toBeTrue();
			} catch ( Exception $e ) {
				$expected = "Failed asserting that 'false' is true";
				if ( $e->getMessage() === $expected ) {
					return;
				}
				throw new Exception( 'toBeTrue() did not have the expected string. Instead it said: ' . $e->getMessage() );
			}
			throw new Exception( 'toBeTrue() did not fail as expected' );
		} );
	} );

	describe( 'toBeFalse()', function() {
		it( 'passes if the actual is false', function() {
			expect( false )->toBeFalse();
		} );

		it( 'fails if its argument is true', function() {
			try {
				expect( true )->toBeFalse();
			} catch ( Exception $e ) {
				return;
			}
			throw new Exception( 'toBeFalse() did not fail as expected' );
		} );

		it( 'fails with an expected string if its argument is true', function() {
			try {
				expect( true )->toBeFalse();
			} catch ( Exception $e ) {
				$expected = "Failed asserting that 'true' is false";
				if ( $e->getMessage() === $expected ) {
					return;
				}
				throw new Exception( 'toBeFalse() did not have the expected string. Instead it said: ' . $e->getMessage() );
			}
			throw new Exception( 'toBeFalse() did not fail as expected' );
		} );
	} );

	describe( 'toEqual()', function() {
		it( 'passes if its argument string equals the actual string', function() {
			expect( 'hello' )->toEqual( 'hello' );
		} );

		it( 'passes if its argument integer equals the actual integer', function() {
			expect( 123 )->toEqual( 123 );
		} );

		it( 'passes if its argument boolean equals the actual boolean', function() {
			expect( true )->toEqual( true );
		} );

		it( 'passes if its argument array equals the actual array', function() {
			expect( [ 1, 5, 9 ] )->toEqual( [ 1, 5, 9 ] );
		} );

		it( 'passes if its argument array equals the actual associative array regardless of order', function() {
			expect( [ 'foo' => 'bar', 'bar' => 'baz' ] )->toEqual( [ 'bar' => 'baz', 'foo' => 'bar' ] );
		} );

		it( 'passes if its argument object equals the actual object', function() {
			$obj = (object) [ 'hi' => 'there' ];
			expect( $obj )->toEqual( $obj );
		} );

		it( 'fails if its argument string does not equal the actual string', function() {
			try {
				expect( 'hello' )->toEqual( 'hi' );
			} catch ( Exception $e ) {
				return;
			}
			throw new Exception( 'toEqual() did not fail as expected' );
		} );

		it( 'fails if its argument boolean does not equal the actual boolean', function() {
			try {
				expect( false )->toEqual( true );
			} catch ( Exception $e ) {
				return;
			}
			throw new Exception( 'toEqual() did not fail as expected' );
		} );

		it( 'fails if its argument array does not equal the actual array', function() {
			try {
				expect( [ 2, 6, 8 ] )->toEqual( [ 1, 5, 9 ] );
			} catch ( Exception $e ) {
				return;
			}
			throw new Exception( 'toEqual() did not fail as expected' );
		} );

		it( 'fails if its argument object does not equal the actual object', function() {
			try {
				$actual = (object) [ 'bye' => 'there' ];
				$obj = (object) [ 'hi' => 'there' ];
				expect( $actual )->toEqual( $obj );
			} catch ( Exception $e ) {
				return;
			}
			throw new Exception( 'toEqual() did not fail as expected' );
		} );

		it( 'fails with an expected string if the argument does not equal the actual', function() {
			try {
				expect( 'hello' )->toEqual( 'hi' );
			} catch ( Exception $e ) {
				$expected = "Failed asserting that 'hello' is equal to 'hi'";
				if ( $e->getMessage() === $expected ) {
					return;
				}
				throw new Exception( 'toEqual() did not have the expected string. Instead it said: ' . $e->getMessage() );
			}
			throw new Exception( 'toEqual() did not fail as expected' );
		} );

		it( 'fails with an expected string if the argument array does not equal the actual array', function() {
			try {
				expect( [ 2, 6, 8 ] )->toEqual( [ 1, 5, 9 ] );
			} catch ( Exception $e ) {
				$expected = "Failed asserting that array (\n  0 => 2,\n  1 => 6,\n  2 => 8,\n) is equal to array (\n  0 => 1,\n  1 => 5,\n  2 => 9,\n)";
				if ( $e->getMessage() === $expected ) {
					return;
				}
				throw new Exception( 'toEqual() did not have the expected string. Instead it said: ' . $e->getMessage() );
			}
			throw new Exception( 'toEqual() did not fail as expected' );
		} );
	} );

	describe( 'toNotEqual()', function() {
		it( 'passes if its argument does not equal the actual', function() {
			expect( 'bye' )->toNotEqual( 'hello' );
		} );

		it( 'fails if its argument equals the actual', function() {
			try {
				expect( 'hi' )->toNotEqual( 'hi' );
			} catch ( Exception $e ) {
				return;
			}
			throw new Exception( 'toNotEqual() did not fail as expected' );
		} );

		it( 'fails with an expected string if the argument equals the actual', function() {
			try {
				expect( 'hi' )->toNotEqual( 'hi' );
			} catch ( Exception $e ) {
				$expected = "Failed asserting that 'hi' is not equal to 'hi'";
				if ( $e->getMessage() === $expected ) {
					return;
				}
				throw new Exception( 'toNotEqual() did not have the expected string. Instead it said: ' . $e->getMessage() );
			}
			throw new Exception( 'toNotEqual() did not fail as expected' );
		} );
	} );

	describe( 'toBeGreaterThan()', function() {
		it( 'passes if the actual is greater than its argument', function() {
			expect( 5 )->toBeGreaterThan( 3 );
		} );

		it( 'fails if the actual is less than its argument', function() {
			try {
				expect( 6 )->toBeGreaterThan( 8 );
			} catch ( Exception $e ) {
				return;
			}
			throw new Exception( 'toBeGreaterThan() did not fail as expected' );
		} );

		it( 'fails if its argument is equal to the actual', function() {
			try {
				expect( 6 )->toBeGreaterThan( 6 );
			} catch ( Exception $e ) {
				return;
			}
			throw new Exception( 'toBeGreaterThan() did not fail as expected' );
		} );

		it( 'fails with an expected string if the actual is less than the argument', function() {
			try {
				expect( 6 )->toBeGreaterThan( 8 );
			} catch ( Exception $e ) {
				$expected = "Failed asserting that 6 is greater than 8";
				if ( $e->getMessage() === $expected ) {
					return;
				}
				throw new Exception( 'toBeGreaterThan() did not have the expected string. Instead it said: ' . $e->getMessage() );
			}
			throw new Exception( 'toBeGreaterThan() did not fail as expected' );
		} );
	} );

	describe( 'toBeLessThan()', function() {
		it( 'passes if the actual is less than its argument', function() {
			expect( 1 )->toBeLessThan( 3 );
		} );

		it( 'fails if the actual is greater than its argument', function() {
			try {
				expect( 10 )->toBeLessThan( 8 );
			} catch ( Exception $e ) {
				return;
			}
			throw new Exception( 'toBeLessThan() did not fail as expected' );
		} );

		it( 'fails if its argument is equal to the actual', function() {
			try {
				expect( 6 )->toBeLessThan( 6 );
			} catch ( Exception $e ) {
				return;
			}
			throw new Exception( 'toBeLessThan() did not fail as expected' );
		} );

		it( 'fails with an expected string if the actual is greater than the argument', function() {
			try {
				expect( 9 )->toBeLessThan( 8 );
			} catch ( Exception $e ) {
				$expected = "Failed asserting that 9 is less than 8";
				if ( $e->getMessage() === $expected ) {
					return;
				}
				throw new Exception( 'toBeLessThan() did not have the expected string. Instead it said: ' . $e->getMessage() );
			}
			throw new Exception( 'toBeLessThan() did not fail as expected' );
		} );
	} );

	describe( 'toContain()', function() {
		it( 'passes if the actual string contains the expected string', function() {
			expect( 'foobar' )->toContain( 'foo' );
		} );

		it( 'fails if the actual string does not contain the expected string', function() {
			try {
				expect( 'foobar' )->toContain( 'hello' );
			} catch ( Exception $e ) {
				return;
			}
			throw new Exception( 'toContain() did not fail as expected' );
		} );

		it( 'passes if the actual array contains the expected element', function() {
			expect( [ 'bar', 'foo' ] )->toContain( 'foo' );
		} );

		it( 'fails if the actual array does not contain the expected element', function() {
			try {
				expect( [ 'bar', 'foo' ] )->toContain( 'hello' );
			} catch ( Exception $e ) {
				return;
			}
			throw new Exception( 'toContain() did not fail as expected' );
		} );

		it( 'fails with an expected string if the actual does not contain the expected', function() {
			try {
				expect( [ 'bar', 'foo' ] )->toContain( 'hello' );
			} catch ( Exception $e ) {
				$expected = "Failed asserting that array (\n  0 => 'bar',\n  1 => 'foo',\n) contains 'hello'";
				if ( $e->getMessage() === $expected ) {
					return;
				}
				throw new Exception( 'toContain() did not have the expected string. Instead it said: ' . $e->getMessage() );
			}
			throw new Exception( 'toContain() did not fail as expected' );
		} );
	} );

	describe( 'toNotContain()', function() {
		it( 'passes if the actual string does not contain the expected string', function() {
			expect( 'barbaz' )->toNotContain( 'foo' );
		} );

		it( 'fails if the actual string contains the expected string', function() {
			try {
				expect( 'helloworld' )->toNotContain( 'hello' );
			} catch ( Exception $e ) {
				return;
			}
			throw new Exception( 'toNotContain() did not fail as expected' );
		} );

		it( 'passes if the actual array does not contain the expected element', function() {
			expect( [ 'bar', 'baz' ] )->toNotContain( 'foo' );
		} );

		it( 'fails if the actual array does contain the expected element', function() {
			try {
				expect( [ 'bar', 'hello' ] )->toNotContain( 'hello' );
			} catch ( Exception $e ) {
				return;
			}
			throw new Exception( 'toNotContain() did not fail as expected' );
		} );

		it( 'fails with an expected string if the actual does contain the expected', function() {
			try {
				expect( [ 'bar', 'hello' ] )->toNotContain( 'hello' );
			} catch ( Exception $e ) {
				$expected = "Failed asserting that array (\n  0 => 'bar',\n  1 => 'hello',\n) does not contain 'hello'";
				if ( $e->getMessage() === $expected ) {
					return;
				}
				throw new Exception( 'toNotContain() did not have the expected string. Instead it said: ' . $e->getMessage() );
			}
			throw new Exception( 'toNotContain() did not fail as expected' );
		} );
	} );
} );
