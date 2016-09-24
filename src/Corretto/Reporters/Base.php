<?php
namespace Corretto\Reporters;

use function \Corretto\color;

class Base {
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
		if ( $skipCount > 0 ) {
			$this->output( "\n$skipCount tests skipped", 'WARN' );
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
			$this->output( $test->getException()->getMessage() . "\n\n", 'FAIL' );
			// TODO: can we get a stack trace that excludes the internals of Corretto?
			$this->output( strval( $test->getException() ) . "\n\n" );
		}, $this->failedTests );
	}
}
