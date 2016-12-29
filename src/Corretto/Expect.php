<?php
namespace Corretto;

function expect( $actual ) {
	return new Expectation( $actual );
}

function extendExpectation( $newExpectation ) {
	Expectation::extendExpectation( $newExpectation );
}

class Expectation {
	private static $extensions = [];

	public static function extendExpectation( $Extension ) {
		self::$extensions[] = $Extension;
	}

	function __construct( $actual ) {
		$this->actual = $actual;
	}

	public function __call( $name, $arguments ) {
		foreach( self::$extensions as $Extension ) {
			$instance = new $Extension( $this->actual );
			if ( method_exists( $instance, $name ) ) {
				return call_user_func_array( [ $instance, $name ], $arguments );
			}
		}
		throw new \Exception( "Call to undefined method Corretto\Expectation::$name()" );
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
		$helper = new Helpers();
		$actual = $helper->recursiveAssocSort( $this->actual );
		$expected = $helper->recursiveAssocSort( $expected );
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
}

extendExpectation( '\Corretto\ContainExpectation' );
