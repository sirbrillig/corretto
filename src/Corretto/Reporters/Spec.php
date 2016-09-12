<?php
namespace Corretto\Reporters;

class Spec extends Base {
	protected $failedTests = [];
	protected $testCount = 0;
	protected $suiteCount = 0;

	public function __construct( $runner ) {
		parent::__construct( $runner );
		$runner->on( 'suite-start', [ $this, 'startSuite' ] );
		$runner->on( 'suite-end', [ $this, 'endSuite' ] );
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

	public function skip( $test ) {
		$this->testCount ++;
		$this->skippedTests[] = $test;
		$this->echoIndent();
		echo ' ~ ' . $test->getName() . "\n";
	}

	public function fail( $test ) {
		$this->testCount ++;
		$this->failedTests[] = $test;
		$this->echoIndent();
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

