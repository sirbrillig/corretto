<?php
namespace Corretto;

function describe( string $name, callable $callable ) {
	$desc = new Description( $name, $callable );
	AllTests::addDescription( $desc );
}

function it( string $name, callable $callable ) {
	$test = new Test( $name, $callable );
	AllTests::addTest( $test );
}

function assert( $expression = false ) {
	if ( ! $expression ) {
		throw new \Exception( "Failed asserting that '" . strval( $expression ) . "' is true" );
	}
}

class Test {
	public function __construct( string $name, callable $callable ) {
		$this->name = $name;
		$this->callable = $callable;
	}

	public function run() {
		try {
			( $this->callable )();
		} catch ( \Exception $e ) {
			echo "'$this->name' failed: ", $e->getMessage(), "\n";
			return;
		}
		echo "'$this->name' passed.\n";
	}
}

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
		( $this->callable )();
		$this->runTests();
	}

	public function runTests() {
		echo $this->name . "\n";
		AllTests::incrementDescriptionLevel();
		$runTest = function( Test $test ) {
			AllTests::runTest( $test );
		};
		array_map( $runTest, $this->tests );
	}
}

class AllTests {
	private static $descriptions = [];
	private static $currentDescription = null;
	private static $currentDescriptionLevel = 0;

	public static function incrementDescriptionLevel() {
		self::$currentDescriptionLevel ++;
	}

	public static function decrementDescriptionLevel() {
		self::$currentDescriptionLevel --;
	}

	public static function addTest( Test $test ) {
		if ( ! self::$currentDescription ) {
			throw new \Exception( 'calls to `it` must be inside a `describe` block' );
		}
		self::$currentDescription->addTest( $test );
	}

	public static function addDescription( Description $description ) {
		if ( self::$currentDescription ) {
			return self::$currentDescription->addDescription( $description );
		}
		self::$descriptions[] = $description;
	}

	public static function getDescriptions() {
		return self::$descriptions;
	}

	public static function run() {
		array_map( [ __CLASS__, 'runDescription' ], self::getDescriptions() );
	}

	public static function runDescription( Description $description ) {
		self::$currentDescription = $description;
		$description->run();
	}

	public static function runTest( Test $test ) {
		$indentLevel = self::$currentDescriptionLevel;
		while( $indentLevel > 0 ) {
			echo '  ';
			$indentLevel --;
		}
		$test->run();
	}
}

require( './tests/assertions.php' );
AllTests::run();
