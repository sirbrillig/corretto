<?php
namespace Corretto;

class AllTests {
	private $testCount = 0;
	private $suites = [];
	private $failures = [];
	private $currentSuites = [];

	public function getCurrentSuiteLevel() {
		return count ( $this->currentSuites );
	}

	public function addFailure( Failure $failure ) {
		$this->failures[] = $failure;
	}

	public function getFailures() {
		return $this->failures;
	}

	public function echoFailures() {
		array_map( [ $this, 'echoFailure' ], $this->getFailures() );
	}

	public function echoFailure( Failure $failure ) {
		echo $failure . "\n";
	}

	public function addTest( Test $test ) {
		$currentSuite = $this->getCurrentSuite();
		if ( ! $currentSuite ) {
			throw new \Exception( 'calls to `it` must be inside a `describe` block' );
		}
		$currentSuite->addTest( $test );
	}

	public function addSuite( Suite $suite ) {
		$suite->on( 'startSuite', [ $this, 'setCurrentSuite' ] );
		$suite->on( 'endSuite', [ $this, 'endCurrentSuite' ] );
		$currentSuite = $this->getCurrentSuite();
		if ( $currentSuite ) {
			$currentSuite->addSuite( $suite );
			return;
		}
		$suite->parent = $this;
		$this->suites[] = $suite;
	}

	public function getName() {
		return '';
	}

	public function getSuites() {
		return $this->suites;
	}

	public function setCurrentSuite( Suite $suite ) {
		// TODO: move this echo to a reporter
		$this->echoIndent();
		echo $suite->getName() . "\n";
		$this->currentSuites[] = $suite;
	}

	public function endCurrentSuite() {
		array_pop( $this->currentSuites );
	}

	public function getCurrentSuite() {
		return end( $this->currentSuites );
	}

	public function run() {
		array_map( [ $this, 'runSuite' ], $this->getSuites() );
		echo "\n";
		$testCount = $this->testCount;
		$failureCount = count( $this->getFailures() );
		if ( $failureCount < 1 ) {
			echo "$testCount tests passed\n";
			return;
		}
		echo "$failureCount of $testCount tests failed:\n\n";
		$this->echoFailures();
	}

	public function runSuite( Suite $suite ) {
		$suite->doForAllTests( [ $this, 'runTest' ] );
	}

	public function runTest( Test $test ) {
		$this->testCount ++;
		$this->echoIndent();
		$test->run();
	}

	public function echoIndent() {
		$indentLevel = $this->getCurrentSuiteLevel();
		while( $indentLevel > 0 ) {
			echo '  ';
			$indentLevel --;
		}
	}
}
