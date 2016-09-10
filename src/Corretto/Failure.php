<?php
namespace Corretto;

class Failure {
	public $test;
	public $exception;

	public function __construct( Test $test, \Exception $exception, array $descriptions = [] ) {
		$this->test = $test;
		$this->exception = $exception;
		$this->descriptions = $descriptions;
	}
}

