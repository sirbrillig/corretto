<?php

use function \Corretto\{describe, it, assert};

describe( 'describe', function() {
	describe( 'when nested', function() {
		it( 'passes if its argument is true', function() {
			assert( false );
			// assert( true );
		} );
	} );

} );
