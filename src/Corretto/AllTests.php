<?php
namespace Corretto;

class AllTests {
	private $testCount = 0;
	private $descriptions = [];
	private $failures = [];
	private $currentDescriptions = [];
	private $currentDescriptionLevel = 0;

	public function incrementDescriptionLevel() {
		$this->currentDescriptionLevel ++;
	}

	public function decrementDescriptionLevel() {
		$this->currentDescriptionLevel --;
	}

	public function addFailure( Test $test, \Exception $e ) {
		$this->failures[] = new Failure( $test, $e, $this->currentDescriptions );
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
		$currentDescription = $this->getCurrentDescription();
		if ( ! $currentDescription ) {
			throw new \Exception( 'calls to `it` must be inside a `describe` block' );
		}
		$currentDescription->addTest( $test );
	}

	public function addDescription( Description $description ) {
		$description->on( 'startDescription', [ $this, 'setCurrentDescription' ] );
		$description->on( 'endDescription', [ $this, 'endCurrentDescription' ] );
		$currentDescription = $this->getCurrentDescription();
		if ( $currentDescription ) {
			$currentDescription->addDescription( $description );
			return;
		}
		$this->descriptions[] = $description;
	}

	public function getDescriptions() {
		return $this->descriptions;
	}

	public function setCurrentDescription( Description $description ) {
		$this->currentDescriptions[] = $description;
	}

	public function endCurrentDescription() {
		array_pop( $this->currentDescriptions );
	}

	public function getCurrentDescription() {
		return end( $this->currentDescriptions );
	}

	public function run() {
		array_map( [ $this, 'runDescription' ], $this->getDescriptions() );
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

	public function runDescription( Description $description ) {
		$description->doForAllTests( [ $this, 'runTest' ] );
	}

	public function runTest( Test $test ) {
		$this->testCount ++;
		$this->echoIndent();
		$test->run();
	}

	public function echoIndent() {
		$indentLevel = $this->currentDescriptionLevel;
		while( $indentLevel > 0 ) {
			echo '  ';
			$indentLevel --;
		}
	}
}
