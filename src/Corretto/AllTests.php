<?php
namespace Corretto;

class AllTests {
	private $testCount = 0;
	private $descriptions = [];
	private $failures = [];
	private $currentDescriptions = [];

	public function getCurrentDescriptionLevel() {
		return count ( $this->currentDescriptions );
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
		$description->parent = $this;
		$this->descriptions[] = $description;
	}

	public function getName() {
		return '';
	}

	public function getDescriptions() {
		return $this->descriptions;
	}

	public function setCurrentDescription( Description $description ) {
		// TODO: move this echo to a reporter
		$this->echoIndent();
		echo $description->getName() . "\n";
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
		$indentLevel = $this->getCurrentDescriptionLevel();
		while( $indentLevel > 0 ) {
			echo '  ';
			$indentLevel --;
		}
	}
}
