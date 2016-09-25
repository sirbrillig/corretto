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

	public function addSuccess( $test ) {
		$this->successTests[] = $test;
	}

	public function addSkip( $test ) {
		$this->skippedTests[] = $test;
	}

	public function addFail( $test ) {
		$this->failedTests[] = $test;
	}


	public function success( $test ) {
		$this->addSuccess( $test );
		$this->output( ' ✓ ', 'OK' );
		$this->output( $test->getFullName() . "\n", 'INFO' );
	}

	public function skip( $test ) {
		$this->addSkip( $test );
		$this->output( ' ~ ' . $test->getFullName() . "\n", 'WARN' );
	}

	public function fail( $test ) {
		$this->addFail( $test );
		$this->output( ' 𝗫 ' . $test->getFullName() . "\n", 'FAIL' );
	}

	public function epilogue() {
		$failureCount = count( $this->failedTests );
		$skipCount = count( $this->skippedTests );
		$successCount = count( $this->successTests );
		$this->output( "\n" );
		if ( $skipCount < 1 && $failureCount < 1 && $successCount < 1 ) {
			$this->output( "no tests were run!\n", 'FAIL' );
			return;
		}
		if ( $skipCount > 0 ) {
			$this->output( "$skipCount tests skipped\n", 'WARN' );
		}
		if ( $successCount > 0 ) {
			$this->output( "$successCount tests passed\n", 'OK' );
		}
		if ( $failureCount > 0 ) {
			$this->output( "$failureCount tests failed:\n\n", 'FAIL' );
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
		$this->output( "\n" );
	}
}
