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
} );
