<?php
namespace Corretto\Reporters;

use function \Corretto\color;

class Base {
	private $successTests = [];
	private $failedTests = [];
	private $skippedTests = [];
	private $testCount = 0;

	public function __construct( $runner ) {
		$this->colorEnabled = $runner->colorEnabled;

		$runner->on( 'test-success', [ $this, 'addSuccess' ] );
		$runner->on( 'test-skip', [ $this, 'addSkip' ] );
		$runner->on( 'test-failure', [ $this, 'addFail' ] );
		$runner->on( 'test-complete', [ $this, 'addComplete' ] );

		$runner->on( 'tests-start', [ $this, 'prologue' ] );
		$runner->on( 'test-success', [ $this, 'success' ] );
		$runner->on( 'test-skip', [ $this, 'skip' ] );
		$runner->on( 'test-failure', [ $this, 'fail' ] );
		$runner->on( 'test-complete', [ $this, 'complete' ] );
		$runner->on( 'tests-end', [ $this, 'epilogue' ] );

		$runner->on( 'list-test', [ $this, 'listTest' ] );
	}

	protected function output( $message, $type = '' ) {
		if ( ! $this->colorEnabled ) {
			echo $message;
			return;
		}
		echo color( $message, $type );
	}

	final public function addSuccess( $test ) {
		$this->successTests[] = $test;
	}

	final public function addSkip( $test ) {
		$this->skippedTests[] = $test;
	}

	final public function addFail( $test ) {
		$this->failedTests[] = $test;
	}

	final public function addComplete() {
		$this->testCount ++;
	}

	public function complete() {
	}

	public function success( $test ) {
		$this->output( ' ✓ ', 'OK' );
		$this->output( $test->getFullName() . PHP_EOL, 'INFO' );
	}

	public function skip( $test ) {
		$this->output( ' ~ ' . $test->getFullName() . PHP_EOL, 'WARN' );
	}

	public function fail( $test ) {
		$this->output( ' 𝗫 ' . $test->getFullName() . PHP_EOL, 'FAIL' );
	}

	public function prologue() {
		$this->output( PHP_EOL );
	}

	public function epilogue() {
		$failureCount = count( $this->failedTests );
		$skipCount = count( $this->skippedTests );
		$successCount = count( $this->successTests );
		$this->output( PHP_EOL );
		if ( $skipCount < 1 && $failureCount < 1 && $successCount < 1 ) {
			$this->output( "no tests were run!" . PHP_EOL, 'FAIL' );
			return;
		}
		if ( $skipCount > 0 ) {
			$this->output( "$skipCount tests skipped" . PHP_EOL, 'WARN' );
		}
		if ( $successCount > 0 ) {
			$this->output( "$successCount tests passed" . PHP_EOL, 'OK' );
		}
		if ( $failureCount > 0 ) {
			$this->output( "$failureCount tests failed:" . PHP_EOL . PHP_EOL, 'FAIL' );
			$this->outputFailures();
		}
		$this->output( PHP_EOL );
	}

	protected function outputFailures() {
		$index = 0;
		array_map( function( $test ) use ( &$index ) {
			$index ++;
			$this->output( $index . '. ' . $test->getFullName() . ': ' );
			$ex = $test->getException();
			$this->output( $ex->getMessage() . PHP_EOL, 'FAIL' );
			if ( $ex instanceof \Corretto\AssertionFailure ) {
				$trace = $ex->getTrace()[0];
				$traceLine = 'at ' . $trace['function'] . ' in '. $trace['file'] . ':' . $trace['line'];
				$this->output( $traceLine . PHP_EOL . PHP_EOL, 'INFO' );
				return;
			}
			$traceLine = 'in '. $ex->getFile() . ':' . $ex->getLine();
			$this->output( $traceLine . PHP_EOL . PHP_EOL, 'INFO' );
			$this->output( strval( $ex ). PHP_EOL . PHP_EOL, 'INFO' );
		}, $this->failedTests );
	}

	public function listTest( $testInfo ) {
		$this->output( $testInfo['fullName'] . PHP_EOL );
	}
}
