<?php

use function \Corretto\{describe, it, assert, assertTrue, assertFalse, assertEquals, assertNotEquals};

describe( 'assert()', function() {
	it( 'passes if its argument is true', function() {
		assert( true );
	} );

	it( 'fails if its argument is false', function() {
		try {
			assert( false );
		} catch ( Exception $e ) {
			return;
		}
		throw new Exception( 'assert(false) did not fail' );
	} );

	it( 'fails with an expected string if its argument is false', function() {
		try {
			assert( false );
		} catch ( Exception $e ) {
			$expected = "Failed asserting that 'false' is true";
			if ( $e->getMessage() === $expected ) {
				return;
			}
			throw new Exception( 'assert(false) did not have the expected string. Instead it said: ' . $e->getMessage() );
		}
		throw new Exception( 'assert(false) did not fail' );
	} );
} );

describe( 'assertTrue()', function() {
	it( 'passes if its argument is true', function() {
		assertTrue( true );
	} );

	it( 'fails if its argument is false', function() {
		try {
			assertTrue( false );
		} catch ( Exception $e ) {
			return;
		}
		throw new Exception( 'assertTrue(false) did not fail' );
	} );

	it( 'fails with an expected string if its argument is false', function() {
		try {
			assertTrue( false );
		} catch ( Exception $e ) {
			$expected = "Failed asserting that 'false' is true";
			if ( $e->getMessage() === $expected ) {
				return;
			}
			throw new Exception( 'assertTrue(false) did not have the expected string. Instead it said: ' . $e->getMessage() );
		}
		throw new Exception( 'assertTrue(false) did not fail' );
	} );
} );

describe( 'assertFalse()', function() {
	it( 'passes if its argument is false', function() {
		assertFalse( false );
	} );

	it( 'fails if its argument is true', function() {
		try {
			assertFalse( true );
		} catch ( Exception $e ) {
			return;
		}
		throw new Exception( 'assertFalse(true) did not fail' );
	} );

	it( 'fails with an expected string if its argument is false', function() {
		try {
			assertFalse( true );
		} catch ( Exception $e ) {
			$expected = "Failed asserting that 'true' is false";
			if ( $e->getMessage() === $expected ) {
				return;
			}
			throw new Exception( 'assertFalse(true) did not have the expected string. Instead it said: ' . $e->getMessage() );
		}
		throw new Exception( 'assertFalse(true) did not fail' );
	} );
} );

describe( 'assertEquals()', function() {
	it( 'passes if its first argument equals its second', function() {
		assertEquals( 'abcd', 'abcd' );
	} );

	it( 'fails if its first argument does not equal its second', function() {
		try {
			assertEquals( 'abcd', 'bcde' );
		} catch ( Exception $e ) {
			return;
		}
		throw new Exception( 'assertEquals() did not fail for unequal strings' );
	} );

	it( 'fails with an expected string if its arguments are not equal', function() {
		try {
			assertEquals( 1234, 5432 );
		} catch ( Exception $e ) {
			$expected = "Failed asserting that 1234 is equal to 5432";
			if ( $e->getMessage() === $expected ) {
				return;
			}
			throw new Exception( 'assertEquals() did not have the expected string. Instead it said: ' . $e->getMessage() );
		}
		throw new Exception( 'assertEquals() did not fail' );
	} );
} );

describe( 'assertNotEquals()', function() {
	it( 'passes if its first argument does not equal its second', function() {
		assertNotEquals( 'abcd', 'bbcd' );
	} );

	it( 'fails if its first argument equals its second', function() {
		try {
			assertNotEquals( 'abcd', 'abcd' );
		} catch ( Exception $e ) {
			return;
		}
		throw new Exception( 'assertNotEquals() did not fail for equal strings' );
	} );

	it( 'fails with an expected string if its arguments are equal', function() {
		try {
			assertNotEquals( 1234, 1234 );
		} catch ( Exception $e ) {
			$expected = "Failed asserting that '1234' is not equal to '1234'";
			if ( $e->getMessage() === $expected ) {
				return;
			}
			throw new Exception( 'assertNotEquals() did not have the expected string. Instead it said: ' . $e->getMessage() );
		}
		throw new Exception( 'assertNotEquals() did not fail' );
	} );
} );
