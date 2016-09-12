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

function it( string $name, callable $callable = null ) {
	global $runner;
	$test = new Test( $name, $callable );
	$runner->addTest( $test );
}

function skip( string $name, callable $callable = null ) {
	global $runner;
	$test = new Test( $name, $callable );
	$test->skip = true;
	$runner->addTest( $test );
}

function assert( $expression = false ) {
	if ( ! $expression ) {
		throw new \Exception( "Failed asserting that '" . var_export( $expression, true ) . "' is true" );
	}
}
