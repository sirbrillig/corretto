<?php
namespace Corretto;

class ContainExpectation {
	function __construct( $actual ) {
		$this->actual = $actual;
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
