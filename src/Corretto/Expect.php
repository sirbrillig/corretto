<?php
namespace Corretto;

function expect( $actual ) {
	return new Expectation( $actual );
}

class Expectation {
	function __construct( $actual ) {
		$this->actual = $actual;
	}

	public function toBeTrue() {
		$expression = $this->actual;
		if ( ! $expression ) {
			throw new AssertionFailure( "Failed asserting that '" . var_export( $expression, true ) . "' is true" );
		}
	}

	public function toBeFalse() {
		$expression = $this->actual;
		if ( $expression ) {
			throw new AssertionFailure( "Failed asserting that '" . var_export( $expression, true ) . "' is false" );
		}
	}

	public function toEqual( $expected ) {
		$actual = $this->actual;
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

	public function toNotEqual( $expected ) {
		$actual = $this->actual;
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
}
