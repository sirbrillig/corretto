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

	public function createAndAddTest( string $name, $callable = null ) {
		if ( $name === 'SKIP' ) {
			$name = $callable;
			$callable = null;
		}
		$test = new Test( $name, $callable );
		$this->addTestToCurrentSuite( $test );
	}

	public function createAndAddSuite( string $name, $callable = null, $maybeCallable = null ) {
		$skip = false;
		if ( $name === 'SKIP' ) {
			$name = $callable;
			$callable = $maybeCallable;
			$skip = true;
		}
		$suite = new Suite( $name, $callable );
		$suite->skip = $skip;
		$this->addSuiteToCurrentSuite( $suite );
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
		$this->emit( 'tests-start' );
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
		if ( $test->shouldSkip() ) {
			return $this->skipTest( $test );
		}
		try {
			$this->tryTest( $test );
		} catch ( \Exception $e ) {
			$test->setException( $e );
			return $this->failTest( $test );
		}
		$this->passTest( $test );
	}

	protected function tryTest( Test $test ) {
		$context = $test->getContext();
		if ( $test->parent && $test->parent->beforeEach ) {
			( $test->parent->beforeEach )( $context );
		}
		( $test->getTest() )( $context );
		if ( $test->parent && $test->parent->afterEach ) {
			( $test->parent->afterEach )( $context );
		}
	}

	protected function skipTest( Test $test ) {
		$this->hasOnePassingTest = true;
		$this->emit( 'test-skip', $test );
	}

	protected function failTest( Test $test ) {
		$this->hasFailures = true;
		$this->emit( 'test-failure', $test );
		$this->emit( 'test-complete', $test );
	}

	protected function passTest( Test $test ) {
		$this->hasOnePassingTest = true;
		$this->emit( 'test-success', $test );
		$this->emit( 'test-complete', $test );
	}
}
