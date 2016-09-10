<?php
namespace Corretto;

class AllTests {
	private static $testCount = 0;
	private static $descriptions = [];
	private static $failures = [];
	private static $currentDescriptions = [];
	private static $currentDescriptionLevel = 0;

	public static function incrementDescriptionLevel() {
		self::$currentDescriptionLevel ++;
	}

	public static function decrementDescriptionLevel() {
		self::$currentDescriptionLevel --;
	}

	public static function addFailure( Test $test, \Exception $e ) {
		self::$failures[] = new Failure( $test, $e, self::$currentDescriptions );
	}

	public static function getFailures() {
		return self::$failures;
	}

	public static function echoFailures() {
		array_map( [ __CLASS__, 'echoFailure' ], self::getFailures() );
	}

	public static function echoFailure( Failure $failure ) {
		echo $failure . "\n";
	}

	public static function addTest( Test $test ) {
		$currentDescription = self::getCurrentDescription();
		if ( ! $currentDescription ) {
			throw new \Exception( 'calls to `it` must be inside a `describe` block' );
		}
		$currentDescription->addTest( $test );
	}

	public static function addDescription( Description $description ) {
		$currentDescription = self::getCurrentDescription();
		if ( $currentDescription ) {
			return $currentDescription->addDescription( $description );
		}
		self::$descriptions[] = $description;
	}

	public static function getDescriptions() {
		return self::$descriptions;
	}

	public static function setCurrentDescription( Description $description ) {
		self::$currentDescriptions[] = $description;
	}

	public static function endCurrentDescription() {
		array_pop( self::$currentDescriptions );
	}

	public static function getCurrentDescription() {
		return end( self::$currentDescriptions );
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
