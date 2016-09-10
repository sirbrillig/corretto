<?php
namespace Corretto;

class Test {
	public function __construct( string $name, callable $callable ) {
		$this->name = $name;
		$this->callable = $callable;
	}

	public function run() {
		try {
			( $this->callable )();
		} catch ( \Exception $e ) {
			AllTests::addFailure( $this, $e );
			echo " X ";
			echo "$this->name\n";
			return;
		}
		echo " √ ";
		echo "$this->name\n";
	}
}

