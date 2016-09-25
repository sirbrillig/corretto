<?php
namespace Corretto\Reporters;

class Dots extends Base {
	protected $suiteCount = 0;

	public function __construct( $runner ) {
		parent::__construct( $runner );
	}

	public function success( $test ) {
		$this->output( '.' );
	}

	public function skip( $test ) {
		$this->output( '~', 'WARN' );
	}

	public function fail( $test ) {
		$this->output( 'F', 'FAIL' );
	}
}


