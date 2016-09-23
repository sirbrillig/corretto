<?php

use function \Corretto\{describe, it, expect};

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
		it( 'passes if its argument equals the actual', function() {
			expect( 'hello' )->toEqual( 'hello' );
		} );

		it( 'fails if its argument does not equal the actual', function() {
			try {
				expect( 'hello' )->toEqual( 'hi' );
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
	} );

	describe( 'toNotEqual()', function() {
		it( 'passes if its argument does not equal the actual', function() {
			expect( 'bye' )->toNotEqual( 'hello' );
		} );

		it( 'fails if its argument equals the actual' );
		it( 'fails with an expected string if the argument equals the actual' );
	} );
} );
