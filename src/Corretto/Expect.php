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
			throw new AssertionFailure( "Failed asserting that " . $actualString . " is equal to " . $expectedString . "" );
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
			throw new AssertionFailure( "Failed asserting that " . $actualString . " is not equal to " . $expectedString . "" );
		}
	}

	public function toBeGreaterThan( $expected ) {
		$actual = $this->actual;
		if ( $actual <= $expected ) {
			$expectedString = var_export( $expected, true );
			$actualString = var_export( $actual, true );
			throw new AssertionFailure( "Failed asserting that " . $actualString . " is greater than " . $expectedString . "" );
		}
	}

	public function toBeLessThan( $expected ) {
		$actual = $this->actual;
		if ( $actual >= $expected ) {
			$expectedString = var_export( $expected, true );
			$actualString = var_export( $actual, true );
			throw new AssertionFailure( "Failed asserting that " . $actualString . " is less than " . $expectedString . "" );
		}
	}

	public function toContain( $expected ) {
		$actual = $this->actual;
		if ( is_string( $actual ) && strpos( $actual, $expected ) === false ) {
			$expectedString = var_export( $expected, true );
			$actualString = var_export( $actual, true );
			throw new AssertionFailure( "Failed asserting that " . $actualString . " contains " . $expectedString . "" );
		}
		if ( is_array( $actual ) && ! in_array( $expected, $actual ) ) {
			$expectedString = var_export( $expected, true );
			$actualString = var_export( $actual, true );
			throw new AssertionFailure( "Failed asserting that " . $actualString . " contains " . $expectedString . "" );
		}
	}

	public function toNotContain( $expected ) {
		$actual = $this->actual;
		if ( is_string( $actual ) && strpos( $actual, $expected ) !== false ) {
			$expectedString = var_export( $expected, true );
			$actualString = var_export( $actual, true );
			throw new AssertionFailure( "Failed asserting that " . $actualString . " does not contain " . $expectedString . "" );
		}
		if ( is_array( $actual ) && in_array( $expected, $actual ) ) {
			$expectedString = var_export( $expected, true );
			$actualString = var_export( $actual, true );
			throw new AssertionFailure( "Failed asserting that " . $actualString . " does not contain " . $expectedString . "" );
		}
	}
}
