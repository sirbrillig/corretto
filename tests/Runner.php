<?php

use function Corretto\describe, Corretto\it;
use function Corretto\assertTrue, Corretto\assertFalse;
use function Corretto\expect;
use Corretto\Suite, Corretto\Test, Corretto\Runner;
use \Spies\Spy;

describe( 'Runner', function() {
	describe( 'createAndAddTest()', function() {
		it( 'adds a new Test to the current suite', function() {
			$spy1 = new Spy();
			$runner = new Runner();
			$runner->createAndAddTest( 'hello', $spy1 );
			$runner->run();
			assertTrue( $spy1->was_called() );
		} );

		it( 'adds a skipped Test if the first argument is "SKIP"', function() {
			$spy1 = new Spy();
			$runner = new Runner();
			$runner->createAndAddTest( 'SKIP', 'hello', $spy1 );
			$runner->run();
			assertFalse( $spy1->was_called() );
		} );
	} );

	describe( 'createAndAddSuite()', function() {
		it( 'adds a new Suite to the current suite', function() {
			$spy1 = new Spy();
			$runner = new Runner();
			$runner->createAndAddSuite( 'hello suite', function( $suite ) use ( &$spy1 ) {
				$suite->addTest( new Test( 'inner test', $spy1 ) );
			} );
			$runner->run();
			assertTrue( $spy1->was_called() );
		} );

		it( 'adds a skipped Suite if the first argument is "SKIP"', function() {
			$spy1 = new Spy();
			$runner = new Runner();
			$runner->createAndAddSuite( 'SKIP', 'hello suite', function( $suite ) use ( &$spy1 ) {
				$suite->addTest( new Test( 'inner test', $spy1 ) );
			} );
			$runner->run();
			assertFalse( $spy1->was_called() );
		} );
	} );

	describe( 'addBeforeToCurrentSuite()', function() {
		it( 'adds the callable to before on the current suite', function() {
			$spy1 = new Spy();
			$spy2 = new Spy();
			$runner = new Runner();
			$suite = new Suite( 'first', function() use ( &$runner, &$spy1, &$spy2 ) {
				$test1 = new Test( 'one', $spy1 );
				$runner->addTestToCurrentSuite( $test1 );
				$runner->addBeforeToCurrentSuite( $spy2 );
			} );
			$runner->addSuiteToCurrentSuite( $suite );
			$runner->run();
			assertTrue( $spy2->was_called_before( $spy1 ) );
			assertTrue( $suite->before === $spy2 );
		} );
	} );

	describe( 'addAfterToCurrentSuite()', function() {
		it( 'adds the callable to after on the current suite', function() {
			$spy1 = new Spy();
			$spy2 = new Spy();
			$runner = new Runner();
			$suite = new Suite( 'first', function() use ( &$runner, &$spy1, &$spy2 ) {
				$test1 = new Test( 'one', $spy1 );
				$runner->addTestToCurrentSuite( $test1 );
				$runner->addAfterToCurrentSuite( $spy2 );
			} );
			$runner->addSuiteToCurrentSuite( $suite );
			$runner->run();
			assertTrue( $spy1->was_called_before( $spy2 ) );
			assertTrue( $suite->after === $spy2 );
		} );
	} );

	describe( 'addBeforeEachToCurrentSuite()', function() {
		it( 'adds the callable to beforeEach on the current suite', function() {
			$spy1 = new Spy();
			$spy2 = new Spy();
			$runner = new Runner();
			$suite = new Suite( 'first', function() use ( &$runner, &$spy1, &$spy2 ) {
				$test1 = new Test( 'one', $spy1 );
				$runner->addTestToCurrentSuite( $test1 );
				$runner->addBeforeEachToCurrentSuite( $spy2 );
			} );
			$runner->addSuiteToCurrentSuite( $suite );
			$runner->run();
			assertTrue( $spy2->was_called_before( $spy1 ) );
			assertTrue( $suite->beforeEach === $spy2 );
		} );
	} );

	describe( 'addAfterEachToCurrentSuite()', function() {
		it( 'adds the callable to afterEach on the current suite', function() {
			$spy1 = new Spy();
			$spy2 = new Spy();
			$runner = new Runner();
			$suite = new Suite( 'first', function() use ( &$runner, &$spy1, &$spy2 ) {
				$test1 = new Test( 'one', $spy1 );
				$runner->addTestToCurrentSuite( $test1 );
				$runner->addAfterEachToCurrentSuite( $spy2 );
			} );
			$runner->addSuiteToCurrentSuite( $suite );
			$runner->run();
			assertTrue( $spy2->was_called() );
			assertTrue( $spy1->was_called_before( $spy2 ) );
		} );
	} );

	describe( 'addTestToCurrentSuite()', function() {
		it( 'adds a test to the current suite', function() {
			$spy1 = new Spy();
			$runner = new Runner();
			$suite = new Suite( 'first', function() use ( &$runner, &$spy1 ) {
				$test1 = new Test( 'one', $spy1 );
				$runner->addTestToCurrentSuite( $test1 );
			} );
			$runner->addSuiteToCurrentSuite( $suite );
			$runner->run();
			assertTrue( $spy1->was_called() );
		} );

		it( 'adds a test to the default suite if not inside a suite', function() {
			$spy1 = new Spy();
			$test1 = new Test( 'one', $spy1 );
			$runner = new Runner();
			$runner->addTestToCurrentSuite( $test1 );
			$runner->run();
			assertTrue( $spy1->was_called() );
		} );
	} );

	describe( 'addSuiteToCurrentSuite()', function() {
		it( 'adds all tests in the suite', function() {
			$added = 0;
			$suite = new Suite( 'first', function() use ( &$suite, &$added ) {
				$test1 = new Test( 'one', function() {} );
				$suite->addTest( $test1 );
				$added ++;
				$suite2 = new Suite( 'second', function() use ( &$suite2, &$added ) {
					$test2 = new Test( 'two', function() {} );
					$suite2->addTest( $test2 );
					$added ++;
					$suite3 = new Suite( 'third', function() use ( &$suite3, &$added ) {
						$test3 = new Test( 'three', function() {} );
						$suite3->addTest( $test3 );
						$added ++;
					} );
					$suite2->addSuite( $suite3 );
				} );
				$suite->addSuite( $suite2 );
			} );
			$runner = new Runner();
			$runner->addSuiteToCurrentSuite( $suite );
			assertTrue( $added === 3 );
		} );

		it( 'adds only tests matching "grep" if it is set', function() {
			$testSpy1 = new Spy();
			$testSpy2 = new Spy();
			$testSpy3 = new Spy();
			$suite = new Suite( 'when bar', function() use ( &$suite, &$testSpy1, &$testSpy2, &$testSpy3 ) {
				$test1 = new Test( 'grep matching', $testSpy1 );
				$test2 = new Test( 'grep missing', $testSpy2 );
				$test3 = new Test( 'grep another matching', $testSpy3 );
				$suite->addTest( $test1 );
				$suite->addTest( $test2 );
				$suite->addTest( $test3 );
			} );
			$runner = new Runner();
			$runner->grep = 'matching';
			$runner->addSuiteToCurrentSuite( $suite );
			$runner->run();
			assertTrue( $testSpy1->was_called() );
			assertTrue( $testSpy3->was_called() );
			assertTrue( ! $testSpy2->was_called() );
		} );

		it( 'allows "grep" to include suite names', function() {
			$testSpy1 = new Spy();
			$testSpy2 = new Spy();
			$testSpy3 = new Spy();
			$suite = new Suite( 'when bar', function() use ( &$suite, &$testSpy1, &$testSpy2, &$testSpy3 ) {
				$test1 = new Test( 'grep matching', $testSpy1 );
				$test2 = new Test( 'grep missing', $testSpy2 );
				$test3 = new Test( 'grep another matching', $testSpy3 );
				$suite->addTest( $test1 );
				$suite->addTest( $test2 );
				$suite->addTest( $test3 );
			} );
			$runner = new Runner();
			$runner->grep = 'when bar';
			$runner->addSuiteToCurrentSuite( $suite );
			$runner->run();
			assertTrue( $testSpy1->was_called() );
			assertTrue( $testSpy3->was_called() );
			assertTrue( $testSpy2->was_called() );
		} );

		it( 'allows "grep" to include suite names and test names combined', function() {
			$testSpy1 = new Spy();
			$testSpy2 = new Spy();
			$testSpy3 = new Spy();
			$suite = new Suite( 'when bar', function() use ( &$suite, &$testSpy1, &$testSpy2, &$testSpy3 ) {
				$test1 = new Test( 'grep matching', $testSpy1 );
				$test2 = new Test( 'grep missing', $testSpy2 );
				$test3 = new Test( 'grep another matching', $testSpy3 );
				$suite->addTest( $test1 );
				$suite->addTest( $test2 );
				$suite->addTest( $test3 );
			} );
			$runner = new Runner();
			$runner->grep = 'when bar grep matching';
			$runner->addSuiteToCurrentSuite( $suite );
			$runner->run();
			assertTrue( $testSpy1->was_called() );
			assertTrue( ! $testSpy3->was_called() );
			assertTrue( ! $testSpy2->was_called() );
		} );
	} );

	describe( 'runTest()', function() {
		it( 'runs the test function', function() {
			$ran = false;
			$test = new Test( 'foo', function() use( &$ran ) {
				$ran = true;
			} );
			$runner = new Runner();
			$runner->runTest( $test );
			assertTrue( $ran );
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
			assertTrue( $ran );
		} );

		it( 'does not add an Exception to the test if the test function does not throw an Exception', function() {
			$test = new Test( 'foo', function() {} );
			$runner = new Runner();
			$runner->runTest( $test );
			assertTrue( ! $test->getException() );
		} );

		it( 'adds the Exception to the test if the test function throws an Exception', function() {
			$test = new Test( 'foo', function() {
				throw new Exception( 'should not see this' );
			} );
			$runner = new Runner();
			$runner->runTest( $test );
			assertTrue( $test->getException() );
		} );

		it( 'passes the test if the test function does not throw an Exception', function() {
			$test = new Test( 'foo', function() {} );
			$ran = false;
			$runner = new Runner();
			$runner->on( 'test-success', function() use ( &$ran ) {
				$ran = true;
			} );
			$runner->runTest( $test );
			assertTrue( $ran );
		} );

		it( 'skips a test if the test is missing a function', function() {
			$test = new Test( 'foo' );
			$skipped = false;
			$runner = new Runner();
			$runner->on( 'test-skip', function() use ( &$skipped ) {
				$skipped = true;
			} );
			$runner->runTest( $test );
			assertTrue( $skipped );
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
			assertTrue( $skipped );
			assertTrue( ! $ran );
		} );
	} );

	describe( 'runSuite()', function() {
		it( 'runs all the tests for a suite', function() {
			$testSpy1 = new Spy();
			$testSpy2 = new Spy();
			$test1 = new Test( 'foo', $testSpy1 );
			$test2 = new Test( 'foo', $testSpy2 );
			$suite = new Suite( 'when bar' );
			$suite->addTest( $test1 );
			$suite->addTest( $test2 );
			$runner = new Runner();
			$runner->runSuite( $suite );
			assertTrue( $testSpy1->was_called() );
			assertTrue( $testSpy2->was_called() );
		} );

		it( 'emits suite-start event when suite begins', function() {
			$testSpy1 = new Spy();
			$test1 = new Test( 'foo', $testSpy1 );
			$eventSpy = new Spy();
			$suite = new Suite( 'event suite' );
			$suite->addTest( $test1 );
			$runner = new Runner();
			$runner->on( 'suite-start', $eventSpy );
			$runner->runSuite( $suite );
			assertTrue( $eventSpy->was_called_before( $testSpy1 ) );
		} );

		it( 'skips all the tests in a suite if the suite is marked skip', function() {
			$testSpy1 = new Spy();
			$skipSpy = new Spy();
			$test = new Test( 'foo', $testSpy1 );
			$runner = new Runner();
			$runner->on( 'test-skip', $skipSpy );
			$suite = new Suite( 'skip suite' );
			$suite->addTest( $test );
			$suite->skip = true;
			$runner->runSuite( $suite );
			assertTrue( $skipSpy->was_called() );
			assertTrue( ! $testSpy1->was_called() );
		} );

		it( 'calls any "beforeEach" function on the suite before each test', function() {
			$testSpy1 = new Spy();
			$testSpy2 = new Spy();
			$test1 = new Test( 'beforeEach test 1', $testSpy1 );
			$test2 = new Test( 'beforeEach test 2', $testSpy2 );
			$suite = new Suite( 'beforeEach suite' );
			$suite->addTest( $test1 );
			$suite->addTest( $test2 );
			$val = 0;
			$suite->beforeEach = function( $context ) use ( &$val ) {
				$val ++;
				$context->foo = $val;
			};
			$runner = new Runner();
			$runner->runSuite( $suite );
			assertTrue( $testSpy1->was_called_when( function( $args ) {
				return $args[0]->foo === 1;
			} ) );
			assertTrue( $testSpy2->was_called_when( function( $args ) {
				return $args[0]->foo === 2;
			} ) );
		} );

		it( 'calls any "beforeEach" function on the suite before each test within a nested suite', function() {
			$testSpy1 = new Spy();
			$testSpy2 = new Spy();
			$test1 = new Test( 'beforeEach test 1', $testSpy1 );
			$test2 = new Test( 'beforeEach test 2', $testSpy2 );
			$suite = new Suite( 'beforeEach suite' );
			$suite2 = new Suite( 'nested suite' );
			$suite2->addTest( $test1 );
			$suite2->addTest( $test2 );
			$suite->addSuite( $suite2 );
			$val = 0;
			$suite->beforeEach = function( $context ) use ( &$val ) {
				$val ++;
				$context->foo = $val;
			};
			$runner = new Runner();
			$runner->runSuite( $suite );
			assertTrue( $testSpy1->was_called_when( function( $args ) {
				return $args[0]->foo === 1;
			} ) );
			assertTrue( $testSpy2->was_called_when( function( $args ) {
				return $args[0]->foo === 2;
			} ) );
		} );

		it( 'calls any "afterEach" function on the suite after each test', function() {
			$testSpy1 = new Spy();
			$testSpy2 = new Spy();
			$test1 = new Test( 'afterEach test 1', $testSpy1 );
			$test2 = new Test( 'afterEach test 2', $testSpy2 );
			$suite = new Suite( 'afterEach suite' );
			$suite->addTest( $test1 );
			$suite->addTest( $test2 );
			$val = 0;
			$suite->afterEach = function( $context ) use ( &$val ) {
				$val ++;
				$context->foo = $val;
			};
			$runner = new Runner();
			$runner->runSuite( $suite );
			assertTrue( $testSpy1->was_called_when( function( $args ) {
				return ( ! isset( $args[0]->foo ) );
			} ) );
			assertTrue( $testSpy2->was_called_when( function( $args ) {
				return $args[0]->foo === 1;
			} ) );
		} );

		it( 'calls any "afterEach" function on the suite after each test within a nested suite', function() {
			$testSpy1 = new Spy();
			$testSpy2 = new Spy();
			$test1 = new Test( 'afterEach test 1', $testSpy1 );
			$test2 = new Test( 'afterEach test 2', $testSpy2 );
			$suite = new Suite( 'afterEach suite' );
			$suite2 = new Suite( 'nested suite' );
			$suite2->addTest( $test1 );
			$suite2->addTest( $test2 );
			$suite->addSuite( $suite2 );
			$val = 0;
			$suite->afterEach = function( $context ) use ( &$val ) {
				$val ++;
				$context->foo = $val;
			};
			$runner = new Runner();
			$runner->runSuite( $suite );
			assertTrue( $testSpy1->was_called_when( function( $args ) {
				return ( ! isset( $args[0]->foo ) );
			} ) );
			assertTrue( $testSpy2->was_called_when( function( $args ) {
				return $args[0]->foo === 1;
			} ) );
		} );

		it( 'calls any "before" function on the suite before all tests in that suite', function() {
			$testSpy1 = new Spy();
			$testSpy2 = new Spy();
			$test1 = new Test( 'before test 1', $testSpy1 );
			$test2 = new Test( 'before test 2', $testSpy2 );
			$suite = new Suite( 'before suite' );
			$suite->addTest( $test1 );
			$suite->addTest( $test2 );
			$val = 0;
			$suite->before = function( $context ) use ( &$val ) {
				$val ++;
				$context->foo = $val;
			};
			$runner = new Runner();
			$runner->runSuite( $suite );
			assertTrue( $testSpy1->was_called_when( function( $args ) {
				return $args[0]->foo === 1;
			} ) );
			assertTrue( $testSpy2->was_called_when( function( $args ) {
				return $args[0]->foo === 1;
			} ) );
		} );

		it( 'calls any "after" function on the suite after all tests in that suite', function() {
			$testSpy1 = new Spy();
			$testSpy2 = new Spy();
			$test1 = new Test( 'after test 1', $testSpy1 );
			$test2 = new Test( 'after test 2', $testSpy2 );
			$suite = new Suite( 'after suite' );
			$suite->addTest( $test1 );
			$suite->addTest( $test2 );
			$val = 0;
			$suite->after = function( $context ) use ( &$val ) {
				$val ++;
				$context->foo = $val;
			};
			$runner = new Runner();
			$runner->runSuite( $suite );
			assertTrue( $testSpy1->was_called_when( function( $args ) {
				return ( ! isset( $args[0]->foo ) );
			} ) );
			assertTrue( $testSpy2->was_called_when( function( $args ) {
				return ( ! isset( $args[0]->foo ) );
			} ) );
			assertTrue( $val === 1 );
		} );
	} );

	describe( 'run()', function() {
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
			$runner->addSuiteToCurrentSuite( $suite1 );
			$runner->addSuiteToCurrentSuite( $suite2 );
			$runner->run();
			assertTrue( $testSpy1->was_called() && $testSpy2->was_called() );
		} );

		it( 'returns true if at least one test ran and no tests failed', function() {
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
			$runner->addSuiteToCurrentSuite( $suite1 );
			$runner->addSuiteToCurrentSuite( $suite2 );
			assertTrue( $runner->run() );
		} );

		it( 'returns false if at least one test ran and one test failed', function() {
			$testSpy1 = new Spy();
			$test1 = new Test( 'foo', $testSpy1 );
			$test2 = new Test( 'foo', function() {
				throw new Exception( 'test fails' );
			} );
			$suite1 = new Suite( 'when bar1', function( $suite ) use ( &$test1 ) {
				$suite->addTest( $test1 );
			} );
			$suite2 = new Suite( 'when bar2', function( $suite ) use ( &$test2 ) {
				$suite->addTest( $test2 );
			} );
			$runner = new Runner();
			$runner->addSuiteToCurrentSuite( $suite1 );
			$runner->addSuiteToCurrentSuite( $suite2 );
			assertFalse( $runner->run() );
		} );


		it( 'returns false if no tests ran', function() {
			$suite1 = new Suite( 'when bar1', function() {} );
			$suite2 = new Suite( 'when bar2', function() {} );
			$runner = new Runner();
			$runner->addSuiteToCurrentSuite( $suite1 );
			$runner->addSuiteToCurrentSuite( $suite2 );
			assertFalse( $runner->run() );
		} );

		it( 'does not trigger "suite-start" event for suites with no tests', function() {
			$suite = new Suite( 'empty suite', function() use ( &$suite ) {
				$testSpy1 = new Spy();
				$testSpy2 = new Spy();
				$test1 = new Test( 'grep missing 1', $testSpy1 );
				$test2 = new Test( 'grep missing 2', $testSpy2 );
				$suite1 = new Suite( 'empty suite 2', function() use ( &$suite1 ) {
					$testSpy3 = new Spy();
					$test3 = new Test( 'grep another missing', $testSpy3 );
					$suite1->addTest( $test3 );
				} );
				$suite->addTest( $test1 );
				$suite->addTest( $test2 );
			} );
			$runner = new Runner();
			$eventSpy = new Spy();
			$runner->on( 'suite-start', $eventSpy );
			$runner->grep = 'matching';
			$runner->addSuiteToCurrentSuite( $suite );
			$runner->run();
			assertTrue( ! $eventSpy->was_called() );
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
			$runner->addSuiteToCurrentSuite( $suite );
			$runner->run();
			assertTrue( $testSpy->was_called_before( $suiteEnd ) );
		} );

		it( 'passes an object from suite beforeEach to tests in that suite', function() {
			$testSpy1 = new Spy();
			$runner = new Runner();
			$suite = new Suite( 'parent', function() use ( &$testSpy1, &$runner ) {
				$runner->addBeforeEachToCurrentSuite( function( $obj ) {
					$obj->name = 'bob';
				} );
				$runner->createAndAddTest( 'child', $testSpy1 );
			} );
			$runner->addSuiteToCurrentSuite( $suite );
			$runner->run();
			assertTrue( $testSpy1->was_called_when( function( $args ) {
				return $args[0]->name === 'bob';
			} ) );
		} );

		it( 'passes an object from suite afterEach to tests in that suite', function() {
			$testSpy1 = new Spy();
			$testSpy2 = new Spy();
			$runner = new Runner();
			$suite = new Suite( 'parent', function() use ( &$testSpy1, &$testSpy2, &$runner ) {
				$runner->addAfterEachToCurrentSuite( function( $obj ) {
					$obj->name = 'bob';
				} );
				$runner->createAndAddTest( 'child', $testSpy1 );
				$runner->createAndAddTest( 'child 2', $testSpy2 );
			} );
			$runner->addSuiteToCurrentSuite( $suite );
			$runner->run();
			assertTrue( $testSpy2->was_called_when( function( $args ) {
				return $args[0]->name === 'bob';
			} ) );
		} );

		it( 'passes an object from suite before to tests in that suite', function() {
			$testSpy1 = new Spy();
			$runner = new Runner();
			$suite = new Suite( 'parent', function() use ( &$testSpy1, &$runner ) {
				$runner->addBeforeToCurrentSuite( function( $obj ) {
					$obj->name = 'bob';
				} );
				$runner->createAndAddTest( 'child', $testSpy1 );
			} );
			$runner->addSuiteToCurrentSuite( $suite );
			$runner->run();
			assertTrue( $testSpy1->was_called_when( function( $args ) {
				return $args[0]->name === 'bob';
			} ) );
		} );

		it( 'passes an object from suite after to tests in that suite', function() {
			$testSpy1 = new Spy();
			$runner = new Runner();
			$suite = new Suite( 'parent', function() use ( &$testSpy1, &$runner ) {
				$runner->createAndAddTest( 'child', function( $obj ) {
					$obj->name = 'bob';
				} );
				$runner->addAfterToCurrentSuite( $testSpy1 );
			} );
			$runner->addSuiteToCurrentSuite( $suite );
			$runner->run();
			assertTrue( $testSpy1->was_called_when( function( $args ) {
				return $args[0]->name === 'bob';
			} ) );
		} );

		it( 'passes an object from suite before to tests within other suites in that suite', function() {
			$testSpy1 = new Spy();
			$runner = new Runner();
			$suite = new Suite( 'parent', function() use ( &$testSpy1, &$runner ) {
				$runner->addBeforeToCurrentSuite( function( $obj ) {
					$obj->name = 'bob';
				} );
				$runner->createAndAddSuite( 'child suite', function() use ( &$testSpy1, &$runner ) {
					$runner->createAndAddTest( 'child', $testSpy1 );
				} );
			} );
			$runner->addSuiteToCurrentSuite( $suite );
			$runner->run();
			assertTrue( $testSpy1->was_called_when( function( $args ) {
				return $args[0]->name === 'bob';
			} ) );
		} );

		it( 'passes an object from suite after to tests within other suites in that suite', function() {
			$testSpy1 = new Spy();
			$runner = new Runner();
			$suite = new Suite( 'parent', function() use ( &$testSpy1, &$runner ) {
				$runner->addAfterToCurrentSuite( $testSpy1 );
				$runner->createAndAddSuite( 'child suite', function() use ( &$runner ) {
					$runner->createAndAddTest( 'child', function( $obj ) {
						$obj->name = 'bob';
					} );
				} );
			} );
			$runner->addSuiteToCurrentSuite( $suite );
			$runner->run();
			assertTrue( $testSpy1->was_called_when( function( $args ) {
				return $args[0]->name === 'bob';
			} ) );
		} );

		it( 'passes an object from suite beforeEach to tests within other suites in that suite', function() {
			$testSpy1 = new Spy();
			$runner = new Runner();
			$suite = new Suite( 'parent', function() use ( &$testSpy1, &$runner ) {
				$runner->addBeforeEachToCurrentSuite( function( $obj ) {
					$obj->name = 'bob';
				} );
				$runner->createAndAddSuite( 'child suite', function() use ( &$testSpy1, &$runner ) {
					$runner->createAndAddTest( 'child', $testSpy1 );
				} );
			} );
			$runner->addSuiteToCurrentSuite( $suite );
			$runner->run();
			assertTrue( $testSpy1->was_called_when( function( $args ) {
				return $args[0]->name === 'bob';
			} ) );
		} );

		it( 'passes an object from suite afterEach to tests within other suites in that suite', function() {
			$testSpy1 = new Spy();
			$runner = new Runner();
			$suite = new Suite( 'parent', function() use ( &$testSpy1, &$runner ) {
				$runner->addAfterEachToCurrentSuite( function( $obj ) {
					$obj->name = 'bob';
				} );
				$runner->createAndAddSuite( 'child suite', function() use ( &$testSpy1, &$runner ) {
					$runner->createAndAddTest( 'child', function() {} );
					$runner->createAndAddTest( 'child two', $testSpy1 );
				} );
			} );
			$runner->addSuiteToCurrentSuite( $suite );
			$runner->run();
			assertTrue( $testSpy1->was_called_when( function( $args ) {
				return $args[0]->name === 'bob';
			} ) );
		} );

		it( 'passes an object from suite beforeEach to beforeEach within other suites in that suite', function() {
			$testSpy1 = new Spy();
			$runner = new Runner();
			$suite = new Suite( 'parent', function() use ( &$testSpy1, &$runner ) {
				$runner->addBeforeEachToCurrentSuite( function( $obj ) {
					$obj->name = 'bob';
				} );
				$runner->createAndAddSuite( 'child suite', function() use ( &$testSpy1, &$runner ) {
					$runner->addBeforeEachToCurrentSuite( function( $obj ) {
						if ( $obj->name === 'bob' ) {
							$obj->name = 'bib';
						}
					} );
					$runner->createAndAddTest( 'child', $testSpy1 );
				} );
			} );
			$runner->addSuiteToCurrentSuite( $suite );
			$runner->run();
			assertTrue( $testSpy1->was_called_when( function( $args ) {
				return $args[0]->name === 'bib';
			} ) );
		} );

		it( 'runs only a single test matching "only" if "only" is set', function() {
			$testSpy1 = new Spy();
			$testSpy2 = new Spy();
			$testSpy3 = new Spy();
			$runner = new Runner();
			$runner->createAndAddSuite( 'parent', function() use ( &$runner, &$testSpy1, &$testSpy2, &$testSpy3 ) {
				$runner->createAndAddTest( 'child 1', $testSpy1 );
				$runner->createAndAddTest( 'child 2', $testSpy2 );
				$runner->createAndAddSuite( 'another parent', function() use ( &$runner, &$testSpy3 ) {
					$runner->createAndAddTest( 'child 1', $testSpy3 );
				} );
			} );
			$runner->only = "parent child 1";
			$runner->run();
			assertTrue( $testSpy1->was_called() );
			assertFalse( $testSpy2->was_called() );
			assertFalse( $testSpy3->was_called() );
		} );
	} );

	describe( 'getTestCount()', function() {
		it( 'returns a count of all tests added to the root suite or nested suites', function() {
			$runner = new Runner();
			$runner->createAndAddSuite( 'parent', function() use ( &$runner ) {
				$runner->createAndAddTest( 'child 1', function() {} );
				$runner->createAndAddSuite( 'child suite', function() use ( &$runner ) {
					$runner->createAndAddTest( 'child 2', function() {} );
					$runner->createAndAddTest( 'child 3', function() {} );
				} );
			} );
			expect( $runner->getTestCount() )->toEqual( 3 );
		} );

		it( 'returns a count of all tests matching "grep"', function() {
			$runner = new Runner();
			$runner->grep = "child suite";
			$runner->createAndAddSuite( 'parent', function() use ( &$runner ) {
				$runner->createAndAddTest( 'child 1', function() {} );
				$runner->createAndAddSuite( 'child suite', function() use ( &$runner ) {
					$runner->createAndAddTest( 'child 2', function() {} );
					$runner->createAndAddTest( 'child 3', function() {} );
				} );
			} );
			expect( $runner->getTestCount() )->toEqual( 2 );
		} );
	} );

	describe( 'listTests()', function() {
		it( 'emits a "list-test" event for each test in each registered suite', function() {
			$runner = new Runner();
			$runner->createAndAddSuite( 'parent', function() use ( &$runner ) {
				$runner->createAndAddTest( 'child 1', function() {} );
				$runner->createAndAddSuite( 'child suite', function() use ( &$runner ) {
					$runner->createAndAddTest( 'child 2', function() {} );
					$runner->createAndAddTest( 'child 3', function() {} );
				} );
			} );
			$eventSpy = new Spy();
			$runner->on( 'list-test', $eventSpy );
			$runner->listTests();
			\Spies\expect_spy( $eventSpy )->to_have_been_called->when( function( $args ) {
				return ( $args[0]['fullName'] === 'parent child 1' );
			} );
			\Spies\expect_spy( $eventSpy )->to_have_been_called->when( function( $args ) {
				return ( $args[0]['fullName'] === 'parent child suite child 2' );
			} );
			\Spies\expect_spy( $eventSpy )->to_have_been_called->when( function( $args ) {
				return ( $args[0]['fullName'] === 'parent child suite child 3' );
			} );
			\Spies\finish_spying();
		} );
	} );
} );
