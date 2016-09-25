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
		$expectedString = var_export( $expected, true );
		$actualString = var_export( $actual, true );
		// print_r() gives a more readable version of objects
		if ( is_object( $expected ) ) {
			$expectedString = print_r( $expected, true );
		}
		if ( is_object( $actual ) ) {
			$actualString = print_r( $actual, true );
		}
		throw new AssertionFailure( "Failed asserting that " . $expectedString . " is equal to " . $actualString . "" );
	}
}

function assertNotEquals( $expected, $actual ) {
	if ( $expected === $actual ) {
		$expectedString = var_export( $expected, true );
		$actualString = var_export( $actual, true );
		// print_r() gives a more readable version of objects
		if ( is_object( $expected ) ) {
			$expectedString = print_r( $expected, true );
		}
		if ( is_object( $actual ) ) {
			$actualString = print_r( $actual, true );
		}
		throw new AssertionFailure( "Failed asserting that " . $expectedString . " is not equal to " . $actualString . "" );
	}
}
