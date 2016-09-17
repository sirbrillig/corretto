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

function assertFalse( $expression = true ) {
	if ( $expression ) {
		throw new \Exception( "Failed asserting that '" . var_export( $expression, true ) . "' is false" );
	}
}

function assertEquals( $first, $second ) {
	if ( $first !== $second ) {
		throw new \Exception( "Failed asserting that '" . var_export( $first, true ) . "' is equal to '" . var_export( $second, true ) . "'" );
	}
}
