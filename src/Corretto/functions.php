<?php
namespace Corretto;

function describe( $sectionDescription, $callable ) {
	$desc = new Description( $sectionDescription, $callable );
	AllTests::addDescription( $desc );
}

function assert( $expression = false ) {
	if ( ! $expression ) {
		throw new \Exception( "Failed asserting that '" . strval( $expression ) . "' is true" );
	}
}

class Description {
	public function __construct( $description, $callable ) {
		$this->description = $description;
		$this->callable = $callable;
	}

	public function run() {
		$it = function( $testDescription, $callable ) {
			try {
				$callable();
			} catch ( \Exception $e ) {
				echo "'$this->description $testDescription' failed: ", $e->getMessage(), "\n";
			}
		};
		( $this->callable )( $it );
	}
}

class AllTests {
	private static $tests;

	public static function addDescription( $descObject ) {
		self::$tests[] = $descObject;
	}

	public static function run() {
		array_map( [ __CLASS__, 'runTest' ], self::$tests );
	}

	public static function runTest( $descObject ) {
		$descObject->run();
	}
}

require( './tests/assertions.php' );
AllTests::run();
