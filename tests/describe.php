<?php

use function \Corretto\{describe, it, assert};
use const \Corretto\SKIP;

describe( 'describe', function() {
	describe( 'when nested', function() {
		it( 'skips tests with no function' );
		it( SKIP, 'skips tests with the SKIP constant as the first argument', function() {
			assert( false );
		} );
		it( 'passes if its argument is true', function() {
			assert( true );
		} );
	} );

	it( 'supports non-nested tests along with nested ones', function() {
		assert( true );
	} );

	describe( 'when multiple tests are nested at the same level', function() {
		it( 'passes if its argument is true', function() {
			assert( true );
		} );
	} );

	describe( SKIP, 'allows skipping whole suites', function() {
		it( 'passes if its argument is true', function() {
			assert( false );
		} );
	} );
} );
