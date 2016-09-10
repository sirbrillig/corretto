<?php
namespace Corretto;

class Description {
	private $descriptions = [];
	private $tests = [];
	private $callable = null;
	public $name = '';

	public function __construct( $name, $callable ) {
		$this->name = $name;
		$this->callable = $callable;
	}

	public function addDescription( Description $description ) {
		$this->descriptions[] = $description;
	}

	public function addTest( Test $test ) {
		$this->tests[] = $test;
	}

	public function run() {
		AllTests::setCurrentDescription( $this );
		( $this->callable )();
		AllTests::echoIndent();
		echo $this->name . "\n";
		AllTests::incrementDescriptionLevel();
		$this->runTests();
		$this->runDescriptions();
		AllTests::decrementDescriptionLevel();
		AllTests::endCurrentDescription();
	}

	public function runDescriptions() {
		$runDescription = function( Description $description ) {
			$description->run();
		};
		array_map( $runDescription, $this->descriptions );
	}

	public function runTests() {
		$runTest = function( Test $test ) {
			AllTests::runTest( $test );
		};
		array_map( $runTest, $this->tests );
	}
}

