<?php
namespace Corretto;

class Runner extends Emitter {
	private $root;
	private $currentlyPreparingSuites = [];
	private $hasFailures = false;
	private $hasOnePassingTest = false;

	public $grep;
	public $colorEnabled = false;

	public function __construct( Suite $root = null ) {
		$this->root = isset( $root ) ? $root : new Suite();
		$this->setCurrentlyPreparingSuite( $this->root );
	}

	public function addTestToCurrentSuite( Test $test ) {
		$currentlyPreparingSuite = $this->getCurrentlyPreparingSuite();
		if ( ! $currentlyPreparingSuite ) {
			throw new \Exception( 'Tests must be added to a suite' );
		}
		$currentlyPreparingSuite->addTest( $test );
	}

	public function addSuiteToCurrentSuite( Suite $suite ) {
		$currentlyPreparingSuite = $this->getCurrentlyPreparingSuite();
		if ( ! $currentlyPreparingSuite ) {
			throw new \Exception( 'Suites must be added to a suite' );
		}
		$this->setCurrentlyPreparingSuite( $suite );
		$currentlyPreparingSuite->addSuite( $suite );
		$this->endCurrentlyPreparingSuite();
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
		$this->runSuite( $this->root );
		$this->emit( 'tests-end' );
		return ! $this->hasFailures && $this->hasOnePassingTest;
	}

	public function runSuite( Suite $suite ) {
		if ( $suite->getDeepTestCount( $this->grep ) < 1 ) {
			return;
		}
		$this->emit( 'suite-start', $suite );
		if ( isset( $suite->before ) ) {
			( $suite->before )( $suite->context );
		}
		array_map( [ $this, 'runTest' ], $suite->getTests( $this->grep ) );
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
