<?php
namespace Corretto;

function assertTrue( $expression = false ) {
	if ( ! $expression ) {
		throw new AssertionFailure( "Failed asserting that '" . var_export( $expression, true ) . "' is true" );
	}
}

function assertFalse( $expression = true ) {
	if ( $expression ) {
		throw new AssertionFailure( "Failed asserting that '" . var_export( $expression, true ) . "' is false" );
	}
}

function assertEquals( $expected, $actual ) {
	if ( $expected !== $actual ) {
		throw new AssertionFailure( "Failed asserting that " . var_export( $expected, true ) . " is equal to " . var_export( $actual, true ) . "" );
	}
}

function assertNotEquals( $expected, $actual ) {
	if ( $expected === $actual ) {
		throw new AssertionFailure( "Failed asserting that " . var_export( $expected, true ) . " is not equal to " . var_export( $actual, true ) . "" );
	}
}
