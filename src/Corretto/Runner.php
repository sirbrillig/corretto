<?php
namespace Corretto;

class Runner extends Emitter {
	private $root;
	private $currentlyPreparingSuites = [];
	private $hasFailures = false;
	private $hasOnePassingTest = false;

	public $grep;
	public $filter;
	public $only;
	public $colorEnabled = false;

	public function __construct( Suite $root = null ) {
		$this->root = isset( $root ) ? $root : new Suite();
		$this->setCurrentlyPreparingSuite( $this->root );
	}

	public function createAndAddTest( $name, $callable = null ) {
		if ( $name === 'SKIP' ) {
			$name = $callable;
			$callable = null;
		}
		$test = new Test( $name, $callable );
		$this->addTestToCurrentSuite( $test );
	}

	public function createAndAddSuite( $name, $callable = null, $maybeCallable = null ) {
		$skip = false;
		if ( $name === 'SKIP' ) {
			$name = $callable;
			$callable = $maybeCallable;
			$skip = true;
		}
		debug( 'adding new suite', $name );
		$suite = new Suite( $name, $callable );
		$suite->skip = $skip;
		$this->addSuiteToCurrentSuite( $suite );
	}

	public function addTestToCurrentSuite( Test $test ) {
		$currentlyPreparingSuite = $this->getCurrentlyPreparingSuite();
		if ( ! $currentlyPreparingSuite ) {
			throw new \Exception( 'Tests must be added to a suite' );
		}
		debug( 'adding test to current suite', $currentlyPreparingSuite->getFullName(), $test->getName() );
		$currentlyPreparingSuite->addTest( $test );
	}

	public function addBeforeToCurrentSuite( callable $callable ) {
		$currentlyPreparingSuite = $this->getCurrentlyPreparingSuite();
		if ( ! $currentlyPreparingSuite ) {
			throw new \Exception( 'before must be added to a suite' );
		}
		$currentlyPreparingSuite->before = $callable;
	}

	public function addAfterToCurrentSuite( callable $callable ) {
		$currentlyPreparingSuite = $this->getCurrentlyPreparingSuite();
		if ( ! $currentlyPreparingSuite ) {
			throw new \Exception( 'after must be added to a suite' );
		}
		$currentlyPreparingSuite->after = $callable;
	}

	public function addBeforeEachToCurrentSuite( callable $callable ) {
		$currentlyPreparingSuite = $this->getCurrentlyPreparingSuite();
		if ( ! $currentlyPreparingSuite ) {
			throw new \Exception( 'beforeEach must be added to a suite' );
		}
		$currentlyPreparingSuite->beforeEach = $callable;
	}

	public function addAfterEachToCurrentSuite( callable $callable ) {
		$currentlyPreparingSuite = $this->getCurrentlyPreparingSuite();
		if ( ! $currentlyPreparingSuite ) {
			throw new \Exception( 'afterEach must be added to a suite' );
		}
		$currentlyPreparingSuite->afterEach = $callable;
	}

	public function addSuiteToCurrentSuite( Suite $suite ) {
		$currentlyPreparingSuite = $this->getCurrentlyPreparingSuite();
		if ( ! $currentlyPreparingSuite ) {
			throw new \Exception( 'Suites must be added to a suite' );
		}
		debug( 'adding test to current suite', $currentlyPreparingSuite->getFullName(), $suite->getName() );
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

	public function getTestCount() {
		return $this->root->getDeepTestCount( $this->grep, $this->filter, $this->only );
	}

	public function runSuite( Suite $suite ) {
		debug( 'running suite', $suite->getFullName() );
		if ( $suite->getDeepTestCount( $this->grep, $this->filter, $this->only ) < 1 ) {
			debug( 'no tests in suite', $suite->getFullName() );
			return;
		}
		$this->emit( 'suite-start', $suite );
		if ( isset( $suite->before ) ) {
			call_user_func( $suite->before, $suite->getContext() );
		}
		array_map( [ $this, 'runTest' ], $suite->getTests( $this->grep, $this->filter, $this->only ) );
		array_map( [ $this, 'runSuite' ], $suite->getSuites() );
		if ( isset( $suite->after ) ) {
			call_user_func( $suite->after, $suite->getContext() );
		}
		$this->emit( 'suite-end', $suite );
	}

	public function runTest( Test $test ) {
		debug( 'running test', $test->getFullName() );
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
		$test->callBeforeEach();
		$test_func = call_user_func( [ $test, 'getTest' ] );
		$this->emit( 'test-start', $test );
		call_user_func( $test_func, $test->getContext() );
		$this->emit( 'test-done', $test );
		$test->callAfterEach();
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

	public function listTests() {
		$this->listSuite( $this->root );
	}

	public function listSuite( $suite ) {
		debug( "listing suite:", $suite->getFullName() );
		array_map( [ $this, 'listTest' ], $suite->getTests( $this->grep, $this->filter, $this->only ) );
		array_map( [ $this, 'listSuite' ], $suite->getSuites() );
	}

	public function listTest( $test ) {
		debug( "listing test:", $test->getFullName() );
		$this->emit( 'list-test', $test->getTestInfo() );
	}
}
