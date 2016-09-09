<?php

use function \Corretto\{describe, it, assert};

describe( 'assert', function() {
	it( 'passes if its argument is true', function() {
		assert( true );
	} );

	it( 'fails if its argument is false', function() {
		try {
			assert( false );
		} catch ( Exception $e ) {
			return;
		}
		throw new Exception( 'assert(false) did not fail' );
	} );

	it( 'fails with an expected string if its argument is false', function() {
		try {
			assert( false );
		} catch ( Exception $e ) {
			$expected = "Failed asserting that '' is true";
			if ( $e->getMessage() === $expected ) {
				return;
			}
			throw new Exception( 'assert(false) did not have the expected string. Instead it said: ' . $e->getMessage() );
		}
		throw new Exception( 'assert(false) did not fail' );
	} );
} );
