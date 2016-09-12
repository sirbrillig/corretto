<?php

use function \Corretto\{describe, it, assert};

describe( 'describe', function() {
	describe( 'when nested', function() {
		it( 'skips tests' );
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
} );
