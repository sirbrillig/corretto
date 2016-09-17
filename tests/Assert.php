<?php

use function \Corretto\{describe, it, assert, assertTrue, assertFalse};

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
