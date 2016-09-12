<?php
namespace Corretto;

class Runner extends Emitter {
	private $suites = [];
	private $currentlyPreparingSuites = [];

	public $grep;

	public function addTest( Test $test ) {
		$currentlyPreparingSuite = $this->getCurrentlyPreparingSuite();
		if ( ! $currentlyPreparingSuite ) {
			throw new \Exception( 'new tests must be part of a test suite' );
		}
		$currentlyPreparingSuite->addTest( $test );
	}

	public function addSuite( Suite $suite ) {
		$suite->on( 'suite-prepare-start', [ $this, 'setCurrentlyPreparingSuite' ] );
		$suite->on( 'suite-prepare-end', [ $this, 'endCurrentlyPreparingSuite' ] );
		$currentlyPreparingSuite = $this->getCurrentlyPreparingSuite();
		if ( $currentlyPreparingSuite ) {
			$currentlyPreparingSuite->addSuite( $suite );
			return;
		}
		$this->suites[] = $suite;
		$suite->grep = $this->grep;
		$suite->prepareSuite();
	}

	protected function setCurrentlyPreparingSuite( Suite $suite ) {
		$this->currentlyPreparingSuites[] = $suite;
	}

	protected function endCurrentlyPreparingSuite() {
		array_pop( $this->currentlyPreparingSuites );
	}

	protected function getCurrentlyPreparingSuite() {
		return end( $this->currentlyPreparingSuites );
	}

	public function run() {
		array_map( [ $this, 'runSuite' ], $this->suites );
		$this->emit( 'tests-end' );
	}

	public function runSuite( Suite $suite ) {
		$suite->on( 'suite-start', function( $data = null ) {
			$this->emit( 'suite-start', $data );
		} );
		$suite->on( 'suite-end', function( $data = null ) {
			$this->emit( 'suite-end', $data );
		} );
		if ( isset( $suite->before ) ) {
			( $suite->before )( $suite->context );
		}
		$suite->doForAllTests( [ $this, 'runTest' ] );
		if ( isset( $suite->after ) ) {
			( $suite->after )( $suite->context );
		}
	}

	public function runTest( Test $test ) {
		$context = new \StdClass();
		if ( $test->parent ) {
			$context = $test->parent->context;
			if ( $test->parent->skip ) {
				$test->skip = true;
			}
		}
		if ( $test->skip || ! $test->getTest() ) {
			$this->emit( 'test-skip', $test );
			return;
		}
		try {
			if ( $test->parent && $test->parent->beforeEach ) {
				( $test->parent->beforeEach )( $context );
			}
			( $test->getTest() )( $context );
			if ( $test->parent && $test->parent->afterEach ) {
				( $test->parent->afterEach )( $context );
			}
		} catch ( \Exception $e ) {
			$test->setException( $e );
			$this->emit( 'test-failure', $test );
			$this->emit( 'test-complete', $test );
			return;
		}
		$this->emit( 'test-success', $test );
		$this->emit( 'test-complete', $test );
	}
}
