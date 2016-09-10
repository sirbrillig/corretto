<?php
namespace Corretto;

class Failure {
	public $test;
	public $exception;

	public function __construct( Test $test, \Exception $exception ) {
		$this->test = $test;
		$this->exception = $exception;
	}
}

