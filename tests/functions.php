<?php

use function \Corretto\{setRunner, describe, it, assertTrue};
use \Corretto\{Suite, Test, Runner};
use \Spies\Spy;

describe( 'Corretto', function() {
	describe( 'it()', function() {
		it( 'creates a test in a suite', function() {
			$runner = new Runner();
			setRunner( $runner );
			$spy1 = new Spy();
			$suite = new Suite( 'first', function() use ( &$spy1 ) {
				it( 'test1', $spy1 );
			} );
			$runner->addSuiteToCurrentSuite( $suite );
			$runner->run();
			assertTrue( $spy1->was_called() );
		} );

		it( 'creates a test outside a suite', function() {
			$runner = new Runner();
			setRunner( $runner );
			$spy1 = new Spy();
			it( 'test1', $spy1 );
			$runner->run();
			assertTrue( $spy1->was_called() );
		} );
	} );
} );
