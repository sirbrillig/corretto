<?php

use function \Corretto\{describe, it, assert};
use \Corretto\{Suite, Test, Runner};
use \Spies\Spy;

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

		it( 'skips a test if the test is missing a function', function() {
			$test = new Test( 'foo' );
			$skipped = false;
			$runner = new Runner();
			$runner->on( 'test-skip', function() use ( &$skipped ) {
				$skipped = true;
			} );
			$runner->runTest( $test );
			assert( $skipped );
		} );

		it( 'skips a test if the test is marked skipped', function() {
			$ran = false;
			$test = new Test( 'foo', function() use ( &$ran ) {
				$ran = true;
			} );
			$test->skip = true;
			$skipped = false;
			$runner = new Runner();
			$runner->on( 'test-skip', function() use ( &$skipped ) {
				$skipped = true;
			} );
			$runner->runTest( $test );
			assert( $skipped );
			assert( ! $ran );
		} );
	} );

	describe( 'runSuite', function() {
		it( 'runs all the tests for a suite', function() {
			$ran = [];
			$test1 = new Test( 'foo', function() use ( &$ran ) {
				$ran[] = 'foo';
			} );
			$test2 = new Test( 'bar', function() use ( &$ran ) {
				$ran[] = 'bar';
			} );
			$suite = new Suite( 'when bar', function( $suite ) use ( &$test1, &$test2 ) {
				$suite->addTest( $test1 );
				$suite->addTest( $test2 );
			} );
			$runner = new Runner();
			$runner->runSuite( $suite );
			assert( count( $ran ) === 2 );
		} );
	} );

	describe( 'run', function() {
		it( 'runs all suites in the runner', function() {
			$testSpy1 = new Spy();
			$testSpy2 = new Spy();
			$test1 = new Test( 'foo', $testSpy1 );
			$test2 = new Test( 'foo', $testSpy2 );
			$suite1 = new Suite( 'when bar1', function( $suite ) use ( &$test1 ) {
				$suite->addTest( $test1 );
			} );
			$suite2 = new Suite( 'when bar2', function( $suite ) use ( &$test2 ) {
				$suite->addTest( $test2 );
			} );
			$runner = new Runner();
			$runner->addSuite( $suite1 );
			$runner->addSuite( $suite2 );
			$runner->run();
			assert( $testSpy1->was_called() && $testSpy2->was_called() );
		} );

		it( 'emits a tests-end event when all tests are complete', function() {
			$testSpy = new Spy();
			$suiteEnd = new Spy();
			$test = new Test( 'foo', $testSpy );
			$suite = new Suite( 'when bar', function( $suite ) use ( &$test ) {
				$suite->addTest( $test );
			} );
			$runner = new Runner();
			$runner->on( 'tests-end', function() use ( &$suiteEnd ) {
				$suiteEnd();
			} );
			$runner->addSuite( $suite );
			$runner->run();
			assert( $testSpy->was_called_before( $suiteEnd ) );
		} );
	} );
} );

