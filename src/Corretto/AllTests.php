<?php
namespace Corretto;

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
