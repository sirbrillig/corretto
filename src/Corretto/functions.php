<?php
namespace Corretto;

$rootSuite;

function setRootSuite( $suite ) {
	global $rootSuite;
	$rootSuite = $suite;
}

function describe( string $name, callable $callable ) {
	global $rootSuite;
	$desc = new Suite( $name, $callable );
	$rootSuite->addSuite( $desc );
}

function it( string $name, callable $callable ) {
	global $rootSuite;
	$test = new Test( $name, $callable );
	$rootSuite->addTest( $test );
}

function assert( $expression = false ) {
	if ( ! $expression ) {
		throw new \Exception( "Failed asserting that '" . strval( $expression ) . "' is true" );
	}
}
