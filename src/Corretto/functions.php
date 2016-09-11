<?php
namespace Corretto;

function setRunner( $suite ) {
	global $runner;
	$runner = $suite;
}

function describe( string $name, callable $callable ) {
	global $runner;
	$desc = new Suite( $name, $callable );
	$runner->addSuite( $desc );
}

function it( string $name, callable $callable ) {
	global $runner;
	$test = new Test( $name, $callable );
	$runner->addTest( $test );
}

function assert( $expression = false ) {
	if ( ! $expression ) {
		throw new \Exception( "Failed asserting that '" . strval( $expression ) . "' is true" );
	}
}
