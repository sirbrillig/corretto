<?php
namespace Corretto;

function assert( $expression = false ) {
	if ( ! $expression ) {
		throw new \Exception( "Failed asserting that '" . var_export( $expression, true ) . "' is true" );
	}
}

function assertTrue( $expression = false ) {
	return assert( $expression );
}
