<?php

use function \Corretto\{describe, it, assert};
use \Corretto\{Test, Runner};

describe( 'Runner', function() {
	describe( 'runTest()', function() {
		it( 'runs the test function', function() {
			$ran = false;
			$test = new Test( 'foo', function() use( &$ran ) {
				$ran = true;
			} );
			$runner = new Runner();
			$runner->runTest( $test );
			assert( $ran );
		} );

		it( 'does not throw an Exception if the test function throws an Exception', function() {
			$test = new Test( 'foo', function() {
				throw new Exception( 'should not see this' );
			} );
			$runner = new Runner();
			try {
				$runner->runTest( $test );
			} catch ( Exception $e ) {
				throw new Exception( 'The runner should not have thrown an Exception but it did' );
			}
		} );

		it( 'fails the test if the test function throws an Exception', function() {
			$test = new Test( 'foo', function() {
				throw new Exception( 'should not see this' );
			} );
			$ran = false;
			$runner = new Runner();
			$runner->on( 'test-failure', function() use ( &$ran ) {
				$ran = true;
			} );
			$runner->runTest( $test );
			assert( $ran );
		} );

		it( 'does not add an Exception to the test if the test function does not throw an Exception', function() {
			$test = new Test( 'foo', function() {} );
			$runner = new Runner();
			$runner->runTest( $test );
			assert( ! $test->getException() );
		} );

		it( 'adds the Exception to the test if the test function throws an Exception', function() {
			$test = new Test( 'foo', function() {
				throw new Exception( 'should not see this' );
			} );
			$runner = new Runner();
			$runner->runTest( $test );
			assert( $test->getException() );
		} );

		it( 'passes the test if the test function does not throw an Exception', function() {
			$test = new Test( 'foo', function() {} );
			$ran = false;
			$runner = new Runner();
			$runner->on( 'test-success', function() use ( &$ran ) {
				$ran = true;
			} );
			$runner->runTest( $test );
			assert( $ran );
		} );
	} );
} );

