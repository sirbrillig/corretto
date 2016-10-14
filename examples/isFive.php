<?php
use function \Corretto\describe;
use function \Corretto\it;

use function \Corretto\assertTrue;
use function \Corretto\assertFalse;

function isFive( $in ) {
	return $in === 5;
}

describe( 'isFive()', function() {
	it( 'returns true if its argument is five', function() {
		assertTrue( isFive( 5 ) );
	} );

	it( 'returns false if its argument is not five', function() {
		assertFalse( isFive( 6 ) );
	} );
} );

