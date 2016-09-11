<?php
namespace Corretto\Reporters;

class Spec extends Base {
	protected $failedTests = [];
	protected $testCount = 0;
	protected $suiteCount = 0;

	public function __construct( $runner ) {
		$runner->on( 'suite-start', [ $this, 'startSuite' ] );
		$runner->on( 'suite-end', [ $this, 'endSuite' ] );
		$runner->on( 'test-success', [ $this, 'success' ] );
		$runner->on( 'test-failure', [ $this, 'fail' ] );
		$runner->on( 'tests-end', [ $this, 'epilogue' ] );
	}

	public function startSuite( $suite ) {
		$this->echoIndent();
		echo $suite->getName() . "\n";
		$this->suiteCount ++;
	}

	public function endSuite() {
		$this->suiteCount --;
	}

	public function success( $test ) {
		$this->testCount ++;
		$this->echoIndent();
		echo ' √ ' . $test->getName() . "\n";
	}

	public function fail( $test ) {
		$this->testCount ++;
		$this->failedTests[] = $test;
		echo ' X ' . $test->getName() . "\n";
	}

	protected function echoIndent() {
		$indentLevel = $this->suiteCount;
		while( $indentLevel > 0 ) {
			echo '  ';
			$indentLevel --;
		}
	}
}

