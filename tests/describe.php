<?php

use function \Corretto\{describe, it, assertTrue};
use const \Corretto\SKIP;

it( 'allows tests outside a suite', function() {
	assertTrue( true );
} );

describe( 'describe', function() {
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
