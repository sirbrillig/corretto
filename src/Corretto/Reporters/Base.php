<?php
namespace Corretto\Reporters;

class Base {
	protected $failedTests = [];
	protected $skippedTests = [];
	protected $testCount = 0;

	public function __construct( $runner ) {
		$runner->on( 'test-success', [ $this, 'success' ] );
		$runner->on( 'test-skip', [ $this, 'skip' ] );
		$runner->on( 'test-failure', [ $this, 'fail' ] );
		$runner->on( 'test-complete', [ $this, 'complete' ] );
		$runner->on( 'tests-end', [ $this, 'epilogue' ] );
	}

	public function complete() {
		$this->testCount ++;
	}

	public function success( $test ) {
		echo ' √ ' . $test->getFullName() . "\n";
	}

	public function skip( $test ) {
		$this->skippedTests[] = $test;
		echo ' ~ ' . $test->getFullName() . "\n";
	}

	public function fail( $test ) {
		$this->failedTests[] = $test;
		echo ' X ' . $test->getFullName() . "\n";
	}

	public function epilogue() {
		$failureCount = count( $this->failedTests );
		$skipCount = count( $this->skippedTests );
		if ( $skipCount > 0 ) {
			echo "\n$skipCount tests skipped";
		}
		if ( $failureCount < 1 ) {
			echo "\n$this->testCount tests passed\n";
			return;
		}
		echo "\n$failureCount of $this->testCount tests failed:\n\n";
		$index = 0;
		array_map( function( $test ) use ( &$index ) {
			$index ++;
			echo $index . '. ' . $test->getFullName() . ': ' . $test->getException()->getMessage() . "\n" . strval( $test->getException() ) . "\n\n";
		}, $this->failedTests );
	}
}
