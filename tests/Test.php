<?php

use function Corretto\describe, Corretto\it;
use function Corretto\expect;
use Corretto\Test, Corretto\Suite;

describe( 'Test', function() {
	describe( 'doesTestMatchString()', function() {
		it( 'returns true for an empty string', function() {
			$test = new Test( 'does something', function() {} );
			expect( $test->doesTestMatchString( '' ) )->toBeTrue();
		} );

		it( 'returns false when the test name does not match', function() {
			$test = new Test( 'does something', function() {} );
			expect( $test->doesTestMatchString( 'flies' ) )->toBeFalse();
		} );

		it( 'returns true when the test name matches', function() {
			$test = new Test( 'does something', function() {} );
			expect( $test->doesTestMatchString( 'something' ) )->toBeTrue();
		} );

		it( 'returns true when the test name matches a parent suite', function() {
			$suite = new Suite( 'myFunc()', function() {} );
			$test = new Test( 'does something', function() {} );
			$suite->addTest( $test );
			expect( $test->doesTestMatchString( 'myFunc()' ) )->toBeTrue();
		} );

		it( 'returns true when the test name matches a parent suite and the test', function() {
			$suite = new Suite( 'myFunc()', function() {} );
			$test = new Test( 'does something', function() {} );
			$suite->addTest( $test );
			expect( $test->doesTestMatchString( 'myFunc() does' ) )->toBeTrue();
		} );
	} );

	describe( 'doesTestMatchPattern()', function() {
		it( 'returns true for an empty string', function() {
			$test = new Test( 'does something', function() {} );
			expect( $test->doesTestMatchPattern( '' ) )->toBeTrue();
		} );

		it( 'returns false when the test name does not match', function() {
			$test = new Test( 'does something', function() {} );
			expect( $test->doesTestMatchPattern( 'flies' ) )->toBeFalse();
		} );

		it( 'returns true when the test name matches', function() {
			$test = new Test( 'does something', function() {} );
			expect( $test->doesTestMatchPattern( 'something' ) )->toBeTrue();
		} );

		it( 'returns true when the test name matches a parent suite', function() {
			$suite = new Suite( 'myFunc()', function() {} );
			$test = new Test( 'does something', function() {} );
			$suite->addTest( $test );
			expect( $test->doesTestMatchPattern( 'myFunc..' ) )->toBeTrue();
		} );

		it( 'returns true when the test name matches a parent suite and the test', function() {
			$suite = new Suite( 'myFunc()', function() {} );
			$test = new Test( 'does something', function() {} );
			$suite->addTest( $test );
			expect( $test->doesTestMatchPattern( 'myFunc.. does' ) )->toBeTrue();
		} );
	} );
} );
