<?php
namespace Corretto;

class Runner extends Emitter {
	private $suites = [];
	private $currentSuites = [];

	public function addTest( Test $test ) {
		$currentSuite = $this->getCurrentSuite();
		if ( ! $currentSuite ) {
			throw new \Exception( 'calls to `it` must be inside a `describe` block' );
		}
		$currentSuite->addTest( $test );
	}

	public function addSuite( Suite $suite ) {
		$suite->on( 'suite-start', [ $this, 'setCurrentSuite' ] );
		$suite->on( 'suite-end', [ $this, 'endCurrentSuite' ] );
		$currentSuite = $this->getCurrentSuite();
		if ( $currentSuite ) {
			$currentSuite->addSuite( $suite );
			return;
		}
		$this->suites[] = $suite;
	}

	protected function setCurrentSuite( Suite $suite ) {
		$this->emit( 'suite-start', $suite );
		$this->currentSuites[] = $suite;
	}

	protected function endCurrentSuite() {
		$this->emit( 'suite-end', $this->getCurrentSuite() );
		array_pop( $this->currentSuites );
	}

	protected function getCurrentSuite() {
		return end( $this->currentSuites );
	}

	public function run() {
		array_map( [ $this, 'runSuite' ], $this->suites );
		$this->emit( 'tests-end' );
	}

	public function runSuite( Suite $suite ) {
		$suite->doForAllTests( [ $this, 'runTest' ] );
	}

	public function runTest( Test $test ) {
		if ( $test->skip || ! $test->getTest() ) {
			$this->emit( 'test-skip', $test );
			return;
		}
		try {
			$context = new \StdClass();
			if ( $test->parent && $test->parent->beforeEach ) {
				( $test->parent->beforeEach )( $context );
			}
			( $test->getTest() )( $context );
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
