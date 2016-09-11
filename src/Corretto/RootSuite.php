<?php
namespace Corretto;

class RootSuite extends Suite {
	private $suites = [];
	private $currentSuites = [];

	public function __construct() {
	}

	public function getCurrentSuiteLevel() {
		return count ( $this->currentSuites );
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

	public function getFullName() {
		return '';
	}

	public function getSuites() {
		return $this->suites;
	}

	public function setCurrentSuite( Suite $suite ) {
		$this->emit( 'suite-start', $suite );
		$this->currentSuites[] = $suite;
	}

	public function endCurrentSuite() {
		$this->emit( 'suite-end', $this->getCurrentSuite() );
		array_pop( $this->currentSuites );
	}

	public function getCurrentSuite() {
		return end( $this->currentSuites );
	}

	public function run() {
		array_map( [ $this, 'runSuite' ], $this->getSuites() );
		$this->emit( 'tests-end' );
	}

	public function runSuite( Suite $suite ) {
		$suite->doForAllTests( [ $this, 'runTest' ] );
	}

	public function runTest( Test $test ) {
		try {
			( $test->getTest() )();
		} catch ( \Exception $e ) {
			$test->setException( $e );
			$this->emit( 'test-failure', $test );
			return;
		}
		$this->emit( 'test-success', $test );
	}

	// TODO: move to reporter
	public function echoIndent() {
		$indentLevel = $this->getCurrentSuiteLevel();
		while( $indentLevel > 0 ) {
			echo '  ';
			$indentLevel --;
		}
	}
}
