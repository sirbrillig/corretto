<?php
namespace Corretto\Reporters;

class Spec extends Base {
	protected $suiteCount = 0;

	public function __construct( $runner ) {
		parent::__construct( $runner );
		$runner->on( 'suite-start', [ $this, 'startSuite' ] );
		$runner->on( 'suite-end', [ $this, 'endSuite' ] );
	}

	public function startSuite( $suite ) {
		$this->echoIndent();
		$name = $suite->getName();
		if ( $name ) {
			$this->output( $name . "\n" );
		}
		$this->suiteCount ++;
	}

	public function endSuite() {
		$this->suiteCount --;
	}

	public function success( $test ) {
		$this->echoIndent();
		$this->output( ' ✓ ', 'OK' );
		$this->output( $test->getName() . "\n", 'INFO' );
	}

	public function skip( $test ) {
		$this->skippedTests[] = $test;
		$this->echoIndent();
		$this->output( ' ~ ' . $test->getName() . "\n", 'WARN' );
	}

	public function fail( $test ) {
		$this->failedTests[] = $test;
		$this->echoIndent();
		$this->output( ' 𝗫 ' . $test->getName() . "\n", 'FAIL' );
	}

	protected function echoIndent() {
		$indentLevel = $this->suiteCount;
		while( $indentLevel > 0 ) {
			echo '  ';
			$indentLevel --;
		}
	}
}

