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

	public function __toString() {
		$getName = function( $description ) {
			return $description->name;
		};
		$str = implode( ' ', array_map( $getName, $this->descriptions ) );
		$str .= ' ' . $this->test->name . ' failed: ' . $this->exception->getMessage();
		return $str;
	}
}

