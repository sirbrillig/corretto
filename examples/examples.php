<?php

use function \Corretto\describe;
use function \Corretto\it;

use function \Corretto\assertTrue;
use function \Corretto\assertFalse;
use function \Corretto\assertEquals;
use function \Corretto\assertNotEquals;

use function \Corretto\test;
use function \Corretto\suite;

use function \Corretto\specify;
use function \Corretto\context;

use function \Corretto\beforeEach;
use function \Corretto\afterEach;
use function \Corretto\before;
use function \Corretto\after;

use function \Corretto\expect;

it( 'allows tests outside a suite', function() {
	assertTrue( true );
} );

test( 'tests can use "test" as well as "it"', function() {
	assertTrue( true );
} );

specify( 'tests can use "specify" as well as "it"', function() {
	assertTrue( true );
} );

describe( 'describe()', function() {
	describe( 'when nested', function() {
		describe( 'more than once', function() {
			it( 'passes if its argument is true', function() {
				assertTrue( true );
			} );
		} );
		it( 'skips tests with no function' );
		it( 'SKIP', 'skips tests with the SKIP string as the first argument', function() {
			assertTrue( false );
		} );
		it( 'passes if its argument is true', function() {
			assertTrue( true );
		} );
	} );

	it( 'supports non-nested tests along with nested ones', function() {
		assertTrue( true );
	} );

	describe( 'when multiple tests are nested at the same level', function() {
		it( 'passes if its argument is true', function() {
			assertTrue( true );
		} );
	} );

	describe( 'SKIP', 'allows skipping whole suites', function() {
		it( 'passes if its argument is true', function() {
			assertTrue( false );
		} );
	} );
} );

context( 'a bunch of tests', function() {
	specify( 'suites can use "context" as well as "describe"', function() {
		assertTrue( true );
	} );
} );

suite( 'my tests', function() {
	test( 'suites can use "suite" as well as "describe"', function() {
		assertTrue( true );
	} );

	suite( 'there are many assertions', function() {
		test( 'assertEquals()', function() {
			$actual = 'expected';
			assertEquals( 'expected', $actual );
		} );

		test( 'assertNotEquals()', function() {
			$actual = 'actual';
			assertNotEquals( 'expected', $actual );
		} );

		test( 'assertTrue()', function() {
			assertTrue( true );
		} );

		test( 'assertFalse()', function() {
			assertFalse( false );
		} );
	} );

	suite( 'expectation syntax also works for assertions', function() {
		suite( 'expect()', function() {
			test( '->toBeTrue()', function() {
				expect( true )->toBeTrue();
			} );

			test( '->toBeFalse()', function() {
				expect( false )->toBeFalse();
			} );

			test( '->toEqual()', function() {
				expect( 'hi' )->toEqual( 'hi' );
			} );

			test( '->toNotEqual()', function() {
				expect( 'hi' )->toNotEqual( 'bye' );
			} );
		} );
	} );
} );

describe( 'set up and tear down', function() {
	$ctx = new \StdClass();

	describe( 'beforeEach()', function() use ( &$ctx ) {
		beforeEach( function() use ( &$ctx ) {
			$ctx->name = 'hello';
		} );

		it( 'sets up the test context', function() use ( &$ctx ) {
			expect( $ctx->name )->toEqual( 'hello' );
			$ctx->name = 'bye';
		} );

		it( 'runs again before each test', function() use ( &$ctx ) {
			expect( $ctx->name )->toNotEqual( 'bye' );
		} );
	} );

	describe( 'before()', function() use ( &$ctx ) {
		before( function() use ( &$ctx ) {
			$ctx->name = 'hello';
		} );

		it( 'sets up the test context', function() use ( &$ctx ) {
			expect( $ctx->name )->toEqual( 'hello' );
			$ctx->name = 'bye';
		} );

		it( 'runs only once before the suite runs', function() use ( &$ctx ) {
			expect( $ctx->name )->toEqual( 'bye' );
		} );
	} );

	describe( 'afterEach()', function() {
		$name = 'hello';
		afterEach( function() use ( &$name ) {
			$name = 'bye';
		} );

		it( 'is run after each test', function() use( &$name ) {
			expect( $name )->toEqual( 'hello' );
		} );

		it( 'runs again after each test', function() use ( &$name ) {
			expect( $name )->toEqual( 'bye' );
		} );
	} );

	$name = 'hello';
	describe( 'after()', function() use( &$name ) {
		after( function() use ( &$name ) {
			$name = 'bye';
		} );

		it( 'is run after all tests in a suite', function() use( &$name ) {
			expect( $name )->toEqual( 'hello' );
		} );
	} );

	describe( 'after() (continued)', function() use( &$name ) {
		it( 'is run at the end of a suite', function() use ( &$name ) {
			expect( $name )->toEqual( 'bye' );
		} );
	} );
} );
