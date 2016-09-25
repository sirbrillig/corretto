<?php
namespace Corretto\Reporters;

use function \Corretto\color;

class Base {
	protected $successTests = [];
	protected $failedTests = [];
	protected $skippedTests = [];
	protected $testCount = 0;

	public function __construct( $runner ) {
		$this->colorEnabled = $runner->colorEnabled;
		$runner->on( 'test-success', [ $this, 'success' ] );
		$runner->on( 'test-skip', [ $this, 'skip' ] );
		$runner->on( 'test-failure', [ $this, 'fail' ] );
		$runner->on( 'test-complete', [ $this, 'complete' ] );
		$runner->on( 'tests-end', [ $this, 'epilogue' ] );
	}

	public function output( string $message, string $type = '' ) {
		if ( ! $this->colorEnabled ) {
			echo $message;
			return;
		}
		echo color( $message, $type );
	}

	public function complete() {
		$this->testCount ++;
	}

	public function success( $test ) {
		$this->successTests[] = $test;
		$this->output( ' √ ' . $test->getFullName() . "\n", 'OK' );
	}

	public function skip( $test ) {
		$this->skippedTests[] = $test;
		$this->output( ' ~ ' . $test->getFullName() . "\n", 'WARN' );
	}

	public function fail( $test ) {
		$this->failedTests[] = $test;
		$this->output( ' X ' . $test->getFullName() . "\n", 'FAIL' );
	}

	public function epilogue() {
		$failureCount = count( $this->failedTests );
		$skipCount = count( $this->skippedTests );
		$successCount = count( $this->successTests );
		if ( $skipCount > 0 ) {
			$this->output( "\n$skipCount tests skipped", 'WARN' );
		}
		if ( $skipCount < 1 && $failureCount < 1 && $successCount < 1 ) {
			$this->output( "\nno tests were run!\n", 'FAIL' );
			return;
		}
		if ( $failureCount < 1 ) {
			$this->output( "\n$this->testCount tests passed\n", 'OK' );
			return;
		}
		$this->output( "\n$failureCount of $this->testCount tests failed:\n\n", 'FAIL' );
		$index = 0;
		array_map( function( $test ) use ( &$index ) {
			$index ++;
			$this->output( $index . '. ' . $test->getFullName() . ': ' );
			$this->output( $test->getException()->getMessage() . "\n", 'FAIL' );
			$trace = $test->getException()->getTrace()[0];
			$traceLine = 'at ' . $trace['function'] . ' ('. $trace['file'] . ':' . $trace['line'] . ')';
			$this->output( $traceLine . "\n\n", 'INFO' );
		}, $this->failedTests );
	}
}
