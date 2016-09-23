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
		return assert( $this->actual );
	}

	public function toBeFalse() {
		return assertFalse( $this->actual );
	}

	public function toEqual( $expected ) {
		return assertEquals( $this->actual, $expected );
	}

	public function toNotEqual( $expected ) {
	}
}
