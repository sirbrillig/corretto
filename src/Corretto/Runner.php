<?php
namespace Corretto;

class Runner extends Emitter {
	private $suites = [];
	private $tests = [];
	private $currentlyPreparingSuites = [];
	private $hasFailures = false;
	private $hasOnePassingTest = false;

	public $grep;
	public $colorEnabled = false;

	public function addTest( Test $test ) {
		$currentlyPreparingSuite = $this->getCurrentlyPreparingSuite();
		if ( ! $currentlyPreparingSuite ) {
			if ( $this->grep && ! preg_match( '/' . $this->grep . '/', $test->getFullName() ) ) {
				return;
			}
			$this->tests[] = $test;
			return;
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
		array_map( [ $this, 'runTest' ], $this->tests );
		array_map( [ $this, 'runSuite' ], $this->suites );
		$this->emit( 'tests-end' );
		return ! $this->hasFailures && $this->hasOnePassingTest;
	}

	public function runSuite( Suite $suite ) {
		if ( $suite->getDeepTestCount() < 1 ) {
			return;
		}
		$this->emit( 'suite-start', $suite );
		if ( isset( $suite->before ) ) {
			( $suite->before )( $suite->context );
		}
		array_map( [ $this, 'runTest' ], $suite->getTests() );
		array_map( [ $this, 'runSuite' ], $suite->getSuites() );
		if ( isset( $suite->after ) ) {
			( $suite->after )( $suite->context );
		}
		$this->emit( 'suite-end', $suite );
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
			$this->hasOnePassingTest = true;
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
			$this->hasFailures = true;
			$this->emit( 'test-failure', $test );
			$this->emit( 'test-complete', $test );
			return;
		}
		$this->hasOnePassingTest = true;
		$this->emit( 'test-success', $test );
		$this->emit( 'test-complete', $test );
	}
}
