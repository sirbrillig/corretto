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
