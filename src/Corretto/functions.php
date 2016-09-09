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
			AllTests::addFailure( $this, $e );
			echo " X ";
			echo "$this->name\n";
			return;
		}
		echo " √ ";
		echo "$this->name\n";
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
		AllTests::setCurrentDescription( $this );
		( $this->callable )();
		AllTests::echoIndent();
		echo $this->name . "\n";
		AllTests::incrementDescriptionLevel();
		$this->runTests();
		$this->runDescriptions();
		AllTests::decrementDescriptionLevel();
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

class Failure {
	public $test;
	public $exception;

	public function __construct( Test $test, \Exception $exception ) {
		$this->test = $test;
		$this->exception = $exception;
	}
}

class AllTests {
	private static $testCount = 0;
	private static $descriptions = [];
	private static $failures = [];
	private static $currentDescription = null;
	private static $currentDescriptionLevel = 0;

	public static function incrementDescriptionLevel() {
		self::$currentDescriptionLevel ++;
	}

	public static function decrementDescriptionLevel() {
		self::$currentDescriptionLevel --;
	}

	public static function addFailure( Test $test, \Exception $e ) {
		self::$failures[] = new Failure( $test, $e );
	}

	public static function getFailures() {
		return self::$failures;
	}

	public static function echoFailures() {
		array_map( [ __CLASS__, 'echoFailure' ], self::getFailures() );
	}

	public static function echoFailure( Failure $failure ) {
		echo "'" . $failure->test->name . "' failed: ", $failure->exception->getMessage(), "\n";
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

	public static function setCurrentDescription( Description $description ) {
		self::$currentDescription = $description;
	}

	public static function run() {
		array_map( [ __CLASS__, 'runDescription' ], self::getDescriptions() );
		echo "\n";
		$testCount = self::$testCount;
		$failureCount = count( self::getFailures() );
		if ( $failureCount < 1 ) {
			echo "$testCount tests passed\n";
			return;
		}
		echo "$failureCount of $testCount tests failed:\n\n";
		self::echoFailures();
	}

	public static function runDescription( Description $description ) {
		$description->run();
	}

	public static function runTest( Test $test ) {
		self::$testCount ++;
		self::echoIndent();
		$test->run();
	}

	public static function echoIndent() {
		$indentLevel = self::$currentDescriptionLevel;
		while( $indentLevel > 0 ) {
			echo '  ';
			$indentLevel --;
		}
	}
}

require( './tests/assertions.php' );
require( './tests/describe.php' );
AllTests::run();
