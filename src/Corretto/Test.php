<?php
namespace Corretto;

class Test {
	private $name;
	private $callable;

	public $parent;

	public function __construct( string $name, callable $callable ) {
		$this->name = $name;
		$this->callable = $callable;
	}

	public function getName() {
		return $this->parent ? $this->parent->getName() . ' ' . $this->name : $this->name;
	}

	public function run() {
		try {
			( $this->callable )();
		} catch ( \Exception $e ) {
			$failure = new Failure( $this, $e );
			$this->parent && $this->parent->addFailure( $failure );
			// TODO: move this output to a reporter
			echo ' X ';
			echo "$this->name\n";
			return;
		}
		// TODO: move this output to a reporter
		echo ' √ ';
		echo "$this->name\n";
	}
}

