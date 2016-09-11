<?php
namespace Corretto;

class Failure {
	public $test;
	public $exception;

	public function __construct( Test $test, \Exception $exception ) {
		$this->test = $test;
		$this->exception = $exception;
	}

	public function __toString() {
		$str = $this->test->getName() . ' failed: ' . $this->exception->getMessage();
		return $str;
	}
}

